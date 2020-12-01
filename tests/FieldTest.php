<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;

/**
 * Тестирование полей
 */
class FieldTest extends ModuleTestCase
{
    /**
     * Тестирование фабричного метода
     */
    public function testCreate(): void
    {
        $field = Field::create(['ID' => 1]);
        $this->assertInstanceOf(Field::class, $field);
    }
}
