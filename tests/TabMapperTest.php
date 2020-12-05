<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\ITab;
use Fi1a\UserSettings\TabCollection;
use Fi1a\UserSettings\TabMapper;

use const PHP_INT_MAX;

/**
 * Тестирование маппера вкладок
 */
class TabMapperTest extends TabsAndFieldsTestCase
{
    /**
     * Тестирование getList
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetList(): void
    {
        $collection = TabMapper::getList([
            'filter' => [
                'CODE' => 'FUS_TEST_TAB1',
            ],
        ]);
        $this->assertInstanceOf(TabCollection::class, $collection);
        $this->assertCount(1, $collection);
    }

    /**
     * Тестирование getById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetById(): void
    {
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertInstanceOf(ITab::class, $tab);
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $tab['ID']);
    }

    /**
     * Тестирование ошибки getById с передачей нулевого идентификатора
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetByIdZeroId(): void
    {
        $this->assertFalse(TabMapper::getById(0));
    }

    /**
     * Тестирование проверки найденных элементов в методе getById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetByIdEmptyResult(): void
    {
        $this->assertFalse(TabMapper::getById(PHP_INT_MAX));
    }

    /**
     * Тестирование метода getActive возвращающего активные табы
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetActive(): void
    {
        $collection = TabMapper::getActive([
            'filter' => [
                'CODE' => 'FUS_TEST_TAB1',
            ],
        ]);
        $this->assertInstanceOf(TabCollection::class, $collection);
        $this->assertCount(1, $collection);
    }
}
