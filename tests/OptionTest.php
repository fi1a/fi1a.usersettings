<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\Option;

/**
 * Тестирование класса реализующего работу со значениями пользовательских настроек
 */
class OptionTest extends TabsAndFieldsTestCase
{
    /**
     * Тестирование метода  getAll
     */
    public function testGetAll(): void
    {
        $values = Option::getInstance()->getAll();
        $this->assertIsArray($values);
        $this->assertCount(2, $values);
    }

    /**
     * Тестирование методов set и get
     */
    public function testSetGet(): void
    {
        $option = Option::getInstance();
        $this->assertFalse($option->get('UF_FUS_TEST_FIELD1'));
        $this->assertTrue($option->set('UF_FUS_TEST_FIELD1', 'value')->isSuccess());
        $this->assertEquals('value', $option->get('UF_FUS_TEST_FIELD1'));
    }
}
