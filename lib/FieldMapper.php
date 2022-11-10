<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\UserFieldTable;
use CUserTypeEntity;
use Fi1a\UserSettings\Internals\FieldsTable;
use Fi1a\UserSettings\Internals\UserFieldLangTable;

/**
 * Маппер полей пользовательских настроек
 */
class FieldMapper implements IFieldMapper
{
    /**
     * @inheritDoc
     */
    public static function getList(array $parameters = []): IFieldCollection
    {
        $fields = new FieldCollection();
        $userFieldIdsAlias = [];

        $fieldsIterator = FieldsTable::getList($parameters);

        // Выберем поля пользовательских настроек
        while ($field = $fieldsIterator->fetch()) {
            $userFieldIdsAlias[$field['UF_ID']] = $field['ID'];

            $fields[$field['ID']] = $field;
        }

        // Выберем пользовательские поля
        if (count($userFieldIdsAlias) > 0) {
            $userFieldIds = array_keys($userFieldIdsAlias);

            $userFieldsIterator = UserFieldTable::getList([
                'filter' => [
                    'ID' => $userFieldIds,
                ],
            ]);

            while ($userField = $userFieldsIterator->fetch()) {
                $userType = UserTypeManager::getInstance()->GetUserType($userField['USER_TYPE_ID']);
                if ($userType) {
                    $userField['USER_TYPE'] = $userType;
                }

                $fields[$userFieldIdsAlias[$userField['ID']]]['UF'] = $userField;
            }

            $userFieldLangIterator = UserFieldLangTable::getList([
                'filter' => [
                    'USER_FIELD_ID' => $userFieldIds,
                ],
            ]);

            while ($userFieldLang = $userFieldLangIterator->fetch()) {
                $fieldId = $userFieldLang['USER_FIELD_ID'];
                $alias = $userFieldIdsAlias[$fieldId];
                $langId = $userFieldLang['LANGUAGE_ID'];
                $fields[$alias]['UF']['EDIT_FORM_LABEL'][$langId] = $userFieldLang['EDIT_FORM_LABEL'];
                $fields[$alias]['UF']['HELP_MESSAGE'][$langId] = $userFieldLang['HELP_MESSAGE'];
            }
        }

        return $fields;
    }

    /**
     * @inheritDoc
     */
    public static function getById(int $id)
    {
        if (!$id) {
            return false;
        }

        $collection = static::getList(['filter' => ['ID' => $id,],]);

        if (!count($collection)) {
            return false;
        }
        $collectionArray = $collection->getArrayCopy();

        return reset($collectionArray);
    }

    /**
     * @inheritDoc
     */
    public static function getByTabId(int $tabId)
    {
        if (!$tabId) {
            return false;
        }

        return static::getList(['filter' => ['TAB_ID' => $tabId,],]);
    }

    /**
     * @inheritDoc
     */
    public static function getActive(array $parameters = []): IFieldCollection
    {
        $parameters['filter']['ACTIVE'] = 1;

        return static::getList($parameters);
    }

    /**
     * @inheritDoc
     */
    public static function getByCode(string $code)
    {
        if (!$code) {
            return false;
        }
        $fieldUf = CUserTypeEntity::GetList([], ['ENTITY_ID' => IOption::ENTITY_ID, 'FIELD_NAME' => $code,])->Fetch();

        if (!$fieldUf) {
            return false;
        }

        $collection = static::getList(['filter' => ['UF_ID' => $fieldUf['ID'],],]);

        if (!count($collection)) {
            return false;
        }
        $collectionArray = $collection->getArrayCopy();

        return reset($collectionArray);
    }
}
