<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult;
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
     * @var int[]
     */
    private static $tabIds = [];

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

    /**
     * Тестирование метода add
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAdd(): void
    {
        $tab1 = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB1',
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ],
            ],
        ]);
        $this->assertInstanceOf(ITab::class, $tab1);
        $result = $tab1->save();
        $this->assertTrue($result->isSuccess());
        self::$tabIds['FUS_TEST_TAB1'] = $result->getId();
    }

    /**
     * Событие до добавления поля
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAdd
     */
    public function testAddEventOnBefore(): void
    {
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            function (Event $event) {
                $result = new EventResult();
                $result->addError(new EntityError('UF_FUS_TEST_BEFORE_ADD'));

                return $result;
            }
        );
        $tab1 = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB2',
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ],
            ],
        ]);
        $this->assertInstanceOf(ITab::class, $tab1);
        $this->assertFalse($tab1->save()->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            $eventHandlerKey
        );

        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            function (Event $event) {
                $result = new EventResult();
                $result->modifyFields([
                    'CODE' => 'FUS_TEST_TAB2_MODIFY',
                ]);

                return $result;
            }
        );
        $tab2 = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB2',
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ],
            ],
        ]);
        $this->assertTrue($tab2->save()->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            $eventHandlerKey
        );
    }
}
