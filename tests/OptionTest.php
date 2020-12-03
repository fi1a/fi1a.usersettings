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
}
