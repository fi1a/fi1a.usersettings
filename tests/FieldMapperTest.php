<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use CUserTypeEntity;
use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\FieldCollectionInterface;
use Fi1a\UserSettings\FieldInterface;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\OptionInterface;

use const PHP_INT_MAX;

/**
 * Тестирование класса маппера полей
 */
class FieldMapperTest extends TabsAndFieldsTestCase
{
    /**
     * Тестирование метода getList
     */
    public function testGetList(): void
    {
        $collection = FieldMapper::getList();
        $this->assertInstanceOf(FieldCollectionInterface::class, $collection);
        $this->assertCount(2, $collection);
    }

    /**
     * Тестирование метода getById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetById(): void
    {
        $this->assertFalse(FieldMapper::getById(0));
        $this->assertFalse(FieldMapper::getById(PHP_INT_MAX));
        $this->assertInstanceOf(FieldInterface::class, FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']));
    }

    /**
     * Тестирование метода getByTabId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetByTabId(): void
    {
        $this->assertFalse(FieldMapper::getByTabId(0));
        $collection = FieldMapper::getByTabId(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertInstanceOf(FieldCollectionInterface::class, $collection);
        $this->assertCount(2, $collection);
    }

    /**
     * Тестирование метода getActive
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetActive(): void
    {
        $collection = FieldMapper::getActive();
        $this->assertInstanceOf(FieldCollectionInterface::class, $collection);
        $this->assertCount(2, $collection);
    }

    /**
     * Возвращает поле по коду
     */
    public function testGetByCode(): void
    {
        $this->assertFalse(FieldMapper::getByCode(''));
        $this->assertFalse(FieldMapper::getByCode('UF_FUS_TEST_UNKNOWN'));
        $this->assertInstanceOf(FieldInterface::class, FieldMapper::getByCode('UF_FUS_TEST_FIELD1'));
    }

    /**
     * Возвращает поле по коду
     */
    public function testGetByCodeUfFound(): void
    {
        $userTypeEntity  = new CUserTypeEntity();
        $userTypeId = $userTypeEntity->Add([
            'ENTITY_ID' => OptionInterface::ENTITY_ID,
            'FIELD_NAME' => 'UF_FUS_TEST_FIELD_CHECK',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => '',
            'SORT' => '500',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SETTINGS' => [
                'DEFAULT_VALUE' => '',
                'SIZE' => '20',
                'ROWS' => '1',
                'MIN_LENGTH' => '0',
                'MAX_LENGTH' => '0',
                'REGEXP' => '',
            ],
            'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
            'ERROR_MESSAGE' => null,
            'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
        ]);
        $this->assertFalse(FieldMapper::getByCode('UF_FUS_TEST_FIELD_CHECK'));
        $userTypeEntity->Delete($userTypeId);
    }
}
