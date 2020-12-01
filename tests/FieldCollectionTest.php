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
        $collection[] = Field::create(['ID' => 3,]);
        $this->assertCount(3, $collection);
    }

    /**
     * Преобразует экземпляры классов в массив
     */
    public function testToArray(): void
    {
        $collection = new FieldCollection();
        $collection[] = ['ID' => 1,];
        $this->assertEquals([['ID' => 1,]], $collection->toArray());
    }
}
