<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldCollection;

/**
 * Тестирование коллекции полей
 */
class FieldCollectionTest extends ModuleTestCase
{
    /**
     * Тестирование фабричного метода
     */
    public function testFactory(): void
    {
        $collection = new FieldCollection();
        $collection[] = ['ID' => 1,];
        $collection[] = ['ID' => 2,];
        $collection[] = new Field(['ID' => 3,]);
        $this->assertCount(3, $collection);
    }
}
