<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\UserFieldTable;
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
        if (!empty($userFieldIdsAlias)) {
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
                $fields[$userFieldIdsAlias[$userFieldLang['USER_FIELD_ID']]]['UF']['EDIT_FORM_LABEL'][$userFieldLang['LANGUAGE_ID']] = $userFieldLang['EDIT_FORM_LABEL'];
                $fields[$userFieldIdsAlias[$userFieldLang['USER_FIELD_ID']]]['UF']['HELP_MESSAGE'][$userFieldLang['LANGUAGE_ID']] = $userFieldLang['HELP_MESSAGE'];
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
}
