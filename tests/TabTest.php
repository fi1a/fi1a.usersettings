<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\ITab;
use Fi1a\UserSettings\Tab;
use ReflectionClass;

/**
 * Тестирование табов
 */
class TabTest extends ModuleTestCase
{
    /**
     * Тестирование фабричного метода
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testCreate(): void
    {
        $tab1 = Tab::create(['ID' => 1,]);
        $reflection = new ReflectionClass($tab1);
        $property = $reflection->getProperty('languages');
        $property->setAccessible(true);
        $property->setValue($tab1, null);
        $this->assertInstanceOf(ITab::class, $tab1);
        $tab2 = Tab::create(['ID' => 2, 'LOCALIZATION' => [
            'ru' => [
                'L_NAME' => '',
                'L_TITLE' => '',
            ],
        ],
        ]);
        $this->assertInstanceOf(ITab::class, $tab2);
    }
}
