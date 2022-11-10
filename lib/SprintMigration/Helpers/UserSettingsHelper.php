<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\SprintMigration\Helpers;

use Bitrix\Main\Localization\Loc;
use CUserFieldEnum;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\IOption;
use Fi1a\UserSettings\Option;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabMapper;
use InvalidArgumentException;
use Sprint\Migration\Exceptions\HelperException;
use Sprint\Migration\Helper;
use Sprint\Migration\Helpers\UserTypeEntityHelper;

/**
 * Хелпер модуля sprint.migration
 */
class UserSettingsHelper extends Helper
{
    /**
     * Возвращает вкладки по фильтру
     *
     * @param mixed[] $filter
     *
     * @return mixed[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabs(array $filter = []): array
    {
        return TabMapper::getList(['filter' => $filter,])->__call('getArrayCopy', []);
    }

    /**
     * Возвращает идентификатор вкладки
     *
     * @return int|bool
     *
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabId(string $code)
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $tab = $this->getTabByCode($code);

        return $tab ? (int) $tab['ID'] : false;
    }

    /**
     * Возвращает идентификатор вкладки, если она есть
     *
     * @throws InvalidArgumentException
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabIdIfExists(string $code): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getTabId($code);

        if ($id) {
            return $id;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_NOT_FOUND', ['#CODE#' => $code,]));
    }

    /**
     * Возвращает вкладку по символьному коду
     *
     * @return mixed[]|bool
     *
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabByCode(string $code)
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $parameters = [
            'filter' => [
                'CODE' => $code,
            ],
            'limit' => 1,
        ];

        $result = TabMapper::getList($parameters)->getArrayCopy();
        $tab = reset($result);

        return $tab ? $tab->getArrayCopy() : false;
    }

    /**
     * Возвращает вкладку по символьному коду, если она есть
     *
     * @return mixed[]
     *
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabByCodeIfExists(string $code): array
    {
        $tab = $this->getTabByCode($code);

        if ($tab) {
            return $tab;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_NOT_FOUND', ['#CODE#' => $code]));
    }

    /**
     * Возвращает вкладку по идентификатору
     *
     * @return mixed[]|bool
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabById(int $id)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $tab = TabMapper::getById($id);

        return $tab ? $tab->getArrayCopy() : false;
    }

    /**
     * Возвращает вкладку по идентификатору, если она есть
     *
     * @return mixed[]
     *
     * @throws InvalidArgumentException
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getTabByIdIfExists(int $id): array
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $tab = $this->getTabById($id);

        if ($tab) {
            return $tab;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_NOT_FOUND_ID', ['#ID#' => $id,]));
    }

    /**
     * Экспорт вкладки
     *
     * @return bool|mixed[]
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportTab(int $id)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $tab = $this->getTabById($id);

        return $tab ? $this->prepareExportTab($tab) : false;
    }

    /**
     * Добавление вкладки
     *
     * @param mixed[] $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function addTab(string $code, array $fields, bool $silent = false): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $fields['CODE'] = $code;

        $tab = Tab::create($fields);

        $result = $tab->add();

        if ($result->isSuccess()) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_TAB_ADD_SUCCESS', ['#CODE#' => $code,]));

            return (int) $result->getId();
        }

        if (!$silent) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_ADD_ERROR', ['#CODE#' => $code,]));
    }

    /**
     * Добавление вкладки, если ее нет
     *
     * @param mixed[] $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function addTabIfNotExists(string $code, array $fields, bool $silent = false): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getTabId($code);

        if ($id) {
            return $id;
        }

        return $this->addTab($code, $fields, $silent);
    }

    /**
     * Обновление вкладки
     *
     * @param mixed[] $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function updateTab(int $id, array $fields, bool $silent = false): int
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $fields['ID'] = $id;
        $tab = Tab::create($fields);
        $result = $tab->update();

        if ($result->isSuccess()) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_TAB_UPDATE_SUCCESS', ['#ID#' => $id,]));

            return $id;
        }

        if (!$silent) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_UPDATE_ERROR', ['#ID#' => $id,]));
    }

    /**
     * Обновление вкладки, если она есть
     *
     * @param mixed[] $fields
     *
     * @return bool|int
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function updateTabIfExists(int $id, array $fields, bool $silent = false)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $tab = $this->getTabById($id);

        if (!$tab) {
            return false;
        }

        return $this->updateTab($id, $fields, $silent);
    }

    /**
     * Сохранение вкладки
     *
     * @param mixed[]  $fields
     *
     * @return int|bool
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function saveTab(string $code, array $fields, bool $silent = false)
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getTabId($code);

        if ($id) {
            return $this->updateTab($id, $fields, $silent);
        }

        return $this->addTab($code, $fields, $silent);
    }

    /**
     * Удаляет вкладку
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function deleteTab(string $code, bool $silent = false): bool
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getTabId($code);

        if (!$id) {
            throw new HelperException(
                Loc::getMessage('FUS_SM_HELPER_TAB_DELETE_ERROR_NOT_FOUND', ['#CODE#' => $code,])
            );
        }

        $tab = Tab::create(['ID' => $id]);
        $result = $tab->delete();

        if ($result->isSuccess()) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_TAB_DELETE_SUCCESS', ['#CODE#' => $code,]));

            return true;
        }

        if (!$silent) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_TAB_DELETE_ERROR', ['#CODE#' => $code,]));
    }

    /**
     * Удаляет вкладку, если она есть
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function deleteTabIfExists(string $code, bool $silent = false): bool
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getTabId($code);

        if (!$id) {
            return false;
        }

        return $this->deleteTab($code, $silent);
    }

    /**
     * Возвращает поля по фильтру
     *
     * @param mixed[] $filter
     *
     * @return mixed[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFields(array $filter = []): array
    {
        return FieldMapper::getList(['filter' => $filter,])->__call('getArrayCopy', []);
    }

    /**
     * Возвращает поле по коду
     *
     * @return bool|mixed[]
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldByCode(string $code)
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $field = FieldMapper::getByCode($code);

        if (!$field) {
            return false;
        }

        $field = $field->getArrayCopy();

        if ($field['UF']['USER_TYPE_ID'] === 'enumeration') {
            $enums = [];
            $iterator = (new CUserFieldEnum())->GetList([], ['USER_FIELD_ID' => $field['UF_ID']]);

            while ($enum = $iterator->Fetch()) {
                $enums[] = $enum;
            }

            $field['UF']['ENUMS'] = $enums;
        }

        return $field;
    }

    /**
     * Возвращает поле по коду, если оно есть
     *
     * @return mixed[]
     *
     * @throws InvalidArgumentException
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldByCodeIfExists(string $code): array
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $field = $this->getFieldByCode($code);

        if ($field) {
            return $field;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_NOT_FOUND', ['#FIELD_NAME#' => $code,]));
    }

    /**
     * Возвращает идентификатор поля по коду
     *
     * @return int|bool
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldId(string $code)
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $field = $this->getFieldByCode($code);

        return $field ? (int) $field['ID'] : false;
    }

    /**
     * Возвращает идентификатор поля по коду, если оно есть
     *
     * @throws InvalidArgumentException
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldIdIfExists(string $code): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getFieldId($code);

        if ($id) {
            return $id;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_NOT_FOUND', ['#FIELD_NAME#' => $code,]));
    }

    /**
     * Возвращает поле по идентификатору
     *
     * @return mixed[]|bool
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldById(int $id)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $parameters = [
            'filter' => [
                'ID' => $id,
            ],
            'limit' => 1,
        ];

        $result = FieldMapper::getList($parameters)->getArrayCopy();
        $field = reset($result);

        if (!$field) {
            return false;
        }

        $field = $field->getArrayCopy();

        if ($field['UF']['USER_TYPE_ID'] === 'enumeration') {
            $enums = [];
            $iterator = (new CUserFieldEnum())->GetList([], ['USER_FIELD_ID' => $field['UF']['ID']]);

            while ($enum = $iterator->Fetch()) {
                $enums[] = $enum;
            }

            $field['UF']['ENUMS'] = $enums;
        }

        return $field;
    }

    /**
     * Возвращает поле по идентификатору, если оно есть
     *
     * @return mixed[]
     *
     * @throws InvalidArgumentException
     * @throws HelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getFieldByIdIfExists(int $id): array
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $field = $this->getFieldById($id);

        if ($field) {
            return $field;
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_NOT_FOUND_ID', ['#ID#' => $id,]));
    }

    /**
     * Экспорт поля
     *
     * @return bool|mixed[]
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportField(int $id)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $field = $this->getFieldById($id);

        return $field ? $this->prepareExportField($field) : false;
    }

    /**
     * Добавляет поле
     *
     * @param mixed[]  $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function addField(string $code, array $fields, bool $silent = false): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        if ($fields['TAB_CODE']) {
            $fields['TAB_ID'] = $this->getTabId($fields['TAB_CODE']);
            unset($fields['TAB_CODE']);
        }

        $fields['UF'] = array_replace_recursive([
            'ENTITY_ID' => '',
            'FIELD_NAME' => '',
            'USER_TYPE_ID' => '',
            'XML_ID' => '',
            'SORT' => 500,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => '',
            'SETTINGS' => [],
            'EDIT_FORM_LABEL' => ['ru' => '', 'en' => ''],
            'LIST_COLUMN_LABEL' => ['ru' => '', 'en' => ''],
            'LIST_FILTER_LABEL' => ['ru' => '', 'en' => ''],
            'ERROR_MESSAGE' => '',
            'HELP_MESSAGE' => '',
        ], $fields['UF']);

        $fields['UF']['ENTITY_ID'] = IOption::ENTITY_ID;
        $fields['UF']['FIELD_NAME'] = $code;

        $enums = [];
        if (is_array($fields['UF']['ENUMS'])) {
            $enums = $fields['UF']['ENUMS'];
            unset($fields['UF']['ENUMS']);
        }

        $field = Field::create($fields);
        $result = $field->add();

        $flag = true;
        if ($result->isSuccess() && $fields['UF']['USER_TYPE_ID'] === 'enumeration' && count($enums)) {
            $flag = (new UserTypeEntityHelper())->setUserTypeEntityEnumValues(
                (int) $result->getData()['UF_ID'],
                $enums
            );
        }

        if ($result->isSuccess() && $flag) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_FIELD_ADD_SUCCESS', ['#FIELD_NAME#' => $code,]));

            return (int) $result->getId();
        }

        if (!$silent && !$result->isSuccess()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_ADD_ERROR', ['#FIELD_NAME#' => $code,]));
    }

    /**
     * Добавляет поле, если его нет
     *
     * @param mixed[]  $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function addFieldIfNotExists(string $code, array $fields, bool $silent = false): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getFieldId($code);

        if ($id) {
            return $id;
        }

        return $this->addField($code, $fields, $silent);
    }

    /**
     * Обновляет поле
     *
     * @param mixed[] $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function updateField(int $id, array $fields, bool $silent = false): int
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $field = $this->getFieldById($id);

        if (!$field) {
            throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_UPDATE_ERROR_NOT_FOUND', ['#ID#' => $id,]));
        }

        $fields = array_replace_recursive($field, $fields);

        if ($fields['TAB_CODE']) {
            $fields['TAB_ID'] = $this->getTabId($fields['TAB_CODE']);
            unset($fields['TAB_CODE']);
        }

        $fields['ID'] = $id;

        $enums = [];
        if (is_array($fields['UF']['ENUMS'])) {
            $enums = $fields['UF']['ENUMS'];
            unset($fields['UF']['ENUMS']);
        }

        unset($fields['UF']['ENTITY_ID']);
        unset($fields['UF']['FIELD_NAME']);
        unset($fields['UF']['MULTIPLE']);

        $field = Field::create($fields);
        $result = $field->update();

        $flag = true;
        if ($result->isSuccess() && $fields['UF']['USER_TYPE_ID'] === 'enumeration') {
            $flag = (new UserTypeEntityHelper())->setUserTypeEntityEnumValues((int) $fields['UF']['ID'], $enums);
        }

        if ($result->isSuccess() && $flag) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_FIELD_UPDATE_SUCCESS', ['#ID#' => $id,]));

            return (int) $result->getId();
        }

        if (!$silent && !$result->isSuccess()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_UPDATE_ERROR', ['#ID#' => $id,]));
    }

    /**
     * Обновляет поле, если оно есть
     *
     * @param mixed[] $fields
     *
     * @return int|bool
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function updateFieldIfExists(int $id, array $fields, bool $silent = false)
    {
        if (!$id) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$id',]));
        }

        $field = $this->getFieldById($id);

        if (!$field) {
            return false;
        }

        return $this->updateField($id, $fields, $silent);
    }

    /**
     * Сохраняет поле
     *
     * @param mixed[]  $fields
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function saveField(string $code, array $fields, bool $silent = false): int
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $id = $this->getFieldId($code);

        if ($id) {
            return $this->updateField($id, $fields, $silent);
        }

        return $this->addField($code, $fields, $silent);
    }

    /**
     * Удаляет поле
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function deleteField(string $code, bool $silent = false): bool
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $field = $this->getFieldByCode($code);

        if (!$field) {
            throw new HelperException(
                Loc::getMessage('FUS_SM_HELPER_FIELD_DELETE_ERROR_NOT_FOUND', ['#FIELD_NAME#' => $code,])
            );
        }

        $instance = Field::create($field);
        $result = $instance->delete();

        if ($result->isSuccess()) {
            $this->outInfoIf(
                !$silent,
                Loc::getMessage('FUS_SM_HELPER_FIELD_DELETE_SUCCESS', ['#FIELD_NAME#' => $code,])
            );

            return true;
        }

        if (!$silent && !$result->isSuccess()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_DELETE_ERROR', ['#FIELD_NAME#' => $code,]));
    }

    /**
     * Удаляет поле, если оно есть
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function deleteFieldIfExists(string $code, bool $silent = false): bool
    {
        if (!$code) {
            throw new InvalidArgumentException(Loc::getMessage('FUS_SM_HELPER_REQUIRE', ['#PARAM#' => '$code',]));
        }

        $field = $this->getFieldByCode($code);

        if (!$field) {
            return false;
        }

        return $this->deleteField($code, $silent);
    }

    /**
     * Возвращает значение поля
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOption(string $key)
    {
        $field = $this->getFieldByCode($key);

        if (!$field) {
            throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_NOT_FOUND', ['#FIELD_NAME#' => $key,]));
        }

        $value = Option::getInstance()->get($key);

        if ($field['UF']['USER_TYPE_ID'] === 'enumeration') {
            foreach ($field['UF']['ENUMS'] as $enum) {
                if ($enum['ID'] === $value) {
                    $value = $enum['XML_ID'];
                }
            }
            unset($enum);
        }

        return $value;
    }

    /**
     * Устанавливает значение поля
     *
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function setOption(string $key, $value, bool $silent = false): bool
    {
        $field = $this->getFieldByCode($key);

        if (!$field) {
            throw new HelperException(Loc::getMessage('FUS_SM_HELPER_FIELD_NOT_FOUND', ['#FIELD_NAME#' => $key,]));
        }

        if ($field['UF']['USER_TYPE_ID'] === 'enumeration') {
            foreach ($field['UF']['ENUMS'] as $enum) {
                if ($enum['XML_ID'] === $value) {
                    $value = $enum['ID'];
                }
            }
        }

        $result = Option::getInstance()->set($key, $value);

        if ($result->isSuccess()) {
            $this->outInfoIf(!$silent, Loc::getMessage('FUS_SM_HELPER_SET_OPTION_SUCCESS', ['#ID#' => $field['ID'],]));

            return true;
        }

        if (!$silent && !$result->isSuccess()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                $this->outError($errorMessage);
            }
            unset($errorMessage);
        }

        throw new HelperException(Loc::getMessage('FUS_SM_HELPER_SET_OPTION_ERROR', ['#ID#' => $field['ID'],]));
    }

    /**
     * Подготавливает вкладку для экспорта
     *
     * @param mixed[] $tab
     *
     * @return mixed[]
     */
    protected function prepareExportTab(array $tab): array
    {
        unset($tab['ID']);

        return $tab;
    }

    /**
     * Подготавливает поле для экспорта
     *
     * @param mixed[] $field
     *
     * @return mixed[]
     *
     * @throws InvalidArgumentException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function prepareExportField(array $field): array
    {
        unset($field['ID']);
        unset($field['UF_ID']);
        unset($field['UF']['ID']);
        unset($field['UF']['ENTITY_ID']);

        if ((int) $field['TAB_ID']) {
            $tab = $this->getTabById((int) $field['TAB_ID']);

            if ($tab) {
                $field['TAB_CODE'] = $tab['CODE'];
            }

            unset($field['TAB_ID']);
        }

        if (is_array($field['UF']['ENUMS'])) {
            $enums = [];
            foreach ($field['UF']['ENUMS'] as $enum) {
                unset($enum['ID']);
                unset($enum['USER_FIELD_ID']);

                $enums[] = $enum;
            }
            unset($enum);

            $field['UF']['ENUMS'] = $enums;
        }

        return $field;
    }
}
