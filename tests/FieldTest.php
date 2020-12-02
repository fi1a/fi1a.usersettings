<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
//use Bitrix\Main\EventResult;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult;
use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabMapper;

/**
 * Тестирование полей
 */
class FieldTest extends ModuleTestCase
{
    /**
     * @var int[]
     */
    private static $tabIds = [];

    /**
     * @var int[]
     */
    private static $fieldIds = [];

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $tab = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB1',
            'LOCALIZATION' => null,
        ]);
        $result = $tab->add();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$tabIds['FUS_TEST_TAB1'] = $result->getId();
        $tab = Tab::create([
            'ACTIVE' => 1,
            'CODE' => 'FUS_TEST_TAB2',
            'LOCALIZATION' => null,
        ]);
        $result = $tab->add();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$tabIds['FUS_TEST_TAB2'] = $result->getId();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        foreach (self::$tabIds as $tabId) {
            TabMapper::getById($tabId)->delete();
        }
        parent::tearDownAfterClass();
    }

    /**
     * Тестирование фабричного метода
     */
    public function testCreate(): void
    {
        $field = Field::create(['ID' => 1]);
        $this->assertInstanceOf(Field::class, $field);
    }

    /**
     * Добавление поля
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAdd(): void
    {
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD1',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertTrue($field->save()->isSuccess());
        self::$fieldIds['UF_FUS_TEST_FIELD1'] = $field['ID'];
    }

    /**
     * Событие до добавления поля
     *
     * @depends testAdd
     */
    public function testAddEventOnBefore(): void
    {
        EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldAdd',
            function (Event $event) {
                $fields = $event->getParameter('fields');
                $result = new EventResult();
                if ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_BEFORE_ADD') {
                    $result->addError(new EntityError('UF_FUS_TEST_BEFORE_ADD'));
                } elseif ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_BEFORE_ADD_S') {
                    $result->modifyFields([
                        'UF' => [
                            'FIELD_NAME' => 'UF_FUS_TEST_BEFORE_ADD_SC',
                        ],
                    ]);
                }

                return $result;
            }
        );
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_BEFORE_ADD',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertFalse($field->save()->isSuccess());

        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_BEFORE_ADD_S',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertTrue($field->save()->isSuccess());
        $this->assertEquals('UF_FUS_TEST_BEFORE_ADD_SC', $field['UF']['FIELD_NAME']);
    }

    /**
     * Ошибка при добавлении пользовательского поля
     *
     * @depends testAdd
     */
    public function testUFErrorOnAdd(): void
    {
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertFalse($field->save()->isSuccess());
    }

    /**
     * Ошибка при добавлении поля
     *
     * @depends testAdd
     */
    public function testFieldAdd(): void
    {
        $field = Field::create([
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD2',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertFalse($field->save()->isSuccess());
    }

    /**
     * Ошибка сохранения при исключении \Throwable
     *
     * @depends testAdd
     */
    public function testCatchThrowableExceptionOnAdd(): void
    {
        EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldAdd',
            function (Event $event) {
                $fields = $event->getParameter('fields');

                if ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_THROWABLE') {
                    throw new \ErrorException();
                }
            }
        );
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_THROWABLE',
                'USER_TYPE_ID' => 'string',
                'XML_ID' => '',
                'SORT' => '500',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'Y',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '',
                    'SIZE' => '20',
                    'ROWS' => '1',
                    'MIN_LENGTH' => '0',
                    'MAX_LENGTH' => '0',
                    'REGEXP' => '',
                ],
                'EDIT_FORM_LABEL' => ['ru' => '', 'en' => '',],
                'ERROR_MESSAGE' => null,
                'HELP_MESSAGE' => ['ru' => '', 'en' => '',],
            ],
        ]);
        $this->assertFalse($field->save()->isSuccess());
    }

    /**
     * Обновление поля
     *
     * @depends testAdd
     */
    public function testUpdate(): void
    {
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $field['TAB_ID'] = self::$tabIds['FUS_TEST_TAB2'];
        $this->assertTrue($field->save()->isSuccess());
    }

    /**
     * Значение идентификатора пользовательского поля при обновлении
     *
     * @depends testUpdate
     */
    public function testUfIdUpdate(): void
    {
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $field['TAB_ID'] = self::$tabIds['FUS_TEST_TAB1'];
        unset($field['UF']['ID']);
        $this->assertTrue($field->save()->isSuccess());
    }

    /**
     * Событие до обновления поля
     *
     * @depends testUpdate
     */
    public function testUpdateEventOnBefore(): void
    {
        EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldUpdate',
            function (Event $event) {
                $fields = $event->getParameter('fields');
                $result = new EventResult();
                if ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_BEFORE_UPD') {
                    $result->addError(new EntityError('UF_FUS_TEST_BEFORE_UPD'));
                } elseif ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_BEFORE_UPD_S') {
                    $result->modifyFields([
                        'UF' => [
                            'FIELD_NAME' => 'UF_FUS_TEST_BEFORE_UPD_SC',
                        ],
                    ]);
                }

                return $result;
            }
        );
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $field['UF']['FIELD_NAME'] = 'UF_FUS_TEST_BEFORE_UPD';
        $this->assertFalse($field->save()->isSuccess());

        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $field['UF']['FIELD_NAME'] = 'UF_FUS_TEST_BEFORE_UPD_S';
        $this->assertTrue($field->save()->isSuccess());
        $this->assertEquals('UF_FUS_TEST_BEFORE_UPD_SC', $field['UF']['FIELD_NAME']);
        $field['UF']['FIELD_NAME'] = 'UF_FUS_TEST_FIELD1';
        $this->assertTrue($field->save()->isSuccess());
    }

    /**
     * Ошибка при добавлении пользовательского поля
     *
     * @depends testUpdate
     */
    public function testUFErrorOnUpdate(): void
    {
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        unset($field['UF']['ID']);
        unset($field['UF_ID']);
        $this->assertFalse($field->update()->isSuccess());
    }

    /**
     * Ошибка обновления при исключении \Throwable
     *
     * @depends testUpdate
     */
    public function testCatchThrowableExceptionOnUpdate(): void
    {
        EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldUpdate',
            function (Event $event) {
                $fields = $event->getParameter('fields');

                if ($fields['UF']['FIELD_NAME'] === 'UF_FUS_TEST_THROWABLE') {
                    throw new \ErrorException();
                }
            }
        );
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $field['UF']['FIELD_NAME'] = 'UF_FUS_TEST_THROWABLE';
        $this->assertFalse($field->save()->isSuccess());
    }

    /**
     * Удаление поля
     *
     * @depends testAdd
     */
    public function testDelete(): void
    {
        $field = FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']);
        $this->assertTrue($field->delete()->isSuccess());
    }
}
