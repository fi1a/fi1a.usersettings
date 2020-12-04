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
use Fi1a\UserSettings\TabMapper;
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
        $result = $tab1->save();
        $this->assertTrue($result->isSuccess());
        self::$tabIds['FUS_TEST_TAB2'] = $result->getId();
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
                    'CODE' => 'FUS_TEST_TAB_MODIFY',
                ]);

                return $result;
            }
        );
        $tab2 = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB_NOT_MODIFY',
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ],
            ],
        ]);
        $this->assertTrue($tab2->save()->isSuccess());
        $this->assertEquals('FUS_TEST_TAB_MODIFY', $tab2['CODE']);
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            $eventHandlerKey
        );
    }

    /**
     * Ошибка сохранения при исключении \Throwable
     *
     * @depends testAdd
     */
    public function testCatchThrowableExceptionOnAdd(): void
    {
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            function (Event $event) {
                throw new \ErrorException();
            }
        );
        $tab = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB2',
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ],
            ],
        ]);
        $this->assertFalse($tab->save()->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabAdd',
            $eventHandlerKey
        );
    }

    /**
     * Обновление таба
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAdd
     */
    public function testUpdate(): void
    {
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);
        $tab['ACTIVE'] = 0;
        $this->assertTrue($tab->save()->isSuccess());
        $tab['ACTIVE'] = 1;
        $this->assertTrue($tab->save()->isSuccess());
    }

    /**
     * Ошибка отсутствия идентификатора при обновлении
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAdd
     */
    public function testUpdateEmptyId(): void
    {
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);
        unset($tab['ID']);
        $this->assertFalse($tab->update()->isSuccess());
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
    public function testUpdateEventOnBefore(): void
    {
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            function (Event $event) {
                $result = new EventResult();
                $result->addError(new EntityError('UF_FUS_TEST_BEFORE_ADD'));

                return $result;
            }
        );
        $tab1 = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertInstanceOf(ITab::class, $tab1);
        $this->assertFalse($tab1->save()->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            $eventHandlerKey
        );

        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            function (Event $event) {
                $result = new EventResult();
                $result->modifyFields([
                    'CODE' => 'FUS_TEST_TAB2_MODIFY',
                ]);

                return $result;
            }
        );
        $tab2 = TabMapper::getById(self::$tabIds['FUS_TEST_TAB2']);
        $this->assertTrue($tab2->save()->isSuccess());
        $this->assertEquals('FUS_TEST_TAB2_MODIFY', $tab2['CODE']);
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            $eventHandlerKey
        );
    }

    /**
     * Ошибка обновления при исключении \Throwable
     *
     * @depends testAdd
     */
    public function testCatchThrowableExceptionOnUpdate(): void
    {
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            function (Event $event) {
                throw new \ErrorException();
            }
        );
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);

        $this->assertFalse($tab->save()->isSuccess());
        EventManager::getInstance()->removeEventHandler(
            self::MODULE_ID,
            'OnBeforeTabUpdate',
            $eventHandlerKey
        );
    }

    /**
     * Тестирование удаления вкладки
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAdd
     */
    public function testDelete(): void
    {
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB2']);
        $this->assertTrue($tab->delete()->isSuccess());
    }

    /**
     * Ошибка отсутствия идентификатора при удалении
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAdd
     */
    public function testDeleteEmptyId(): void
    {
        $tab = TabMapper::getById(self::$tabIds['FUS_TEST_TAB1']);
        unset($tab['ID']);
        $this->assertFalse($tab->delete()->isSuccess());
    }
}
