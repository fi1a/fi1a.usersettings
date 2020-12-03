<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\IField;
use Fi1a\UserSettings\IFieldCollection;

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
        $this->assertInstanceOf(IFieldCollection::class, $collection);
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
        $this->assertInstanceOf(IField::class, FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']));
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
        $this->assertInstanceOf(IFieldCollection::class, $collection);
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
        $this->assertInstanceOf(IFieldCollection::class, $collection);
        $this->assertCount(2, $collection);
    }
}
