<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabCollection;

/**
 * Тестирование коллекции вкладок
 */
class TabCollectionTest extends ModuleTestCase
{
    /**
     * Тестирование фабричного метода
     */
    public function testFactory(): void
    {
        $collection = new TabCollection();
        $collection[] = ['ID' => 1,];
        $collection[] = ['ID' => 2,];
        $collection[] = Tab::create(['ID' => 3,]);
        $this->assertCount(3, $collection);
    }

    /**
     * Преобразует экземпляры классов в массив
     */
    public function testToArray(): void
    {
        $collection = new TabCollection();
        $collection[] = ['ID' => 1,];
        $this->assertEquals([['ID' => 1,]], $collection->toArray());
    }
}
