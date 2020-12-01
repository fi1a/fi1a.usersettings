<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
//use Bitrix\Main\EventResult;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult;
use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;
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
    }

    /**
     * Событие до добавления поля
     */
    public function testAddEventOnBefore(): void
    {
        EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldAdd',
            function (Event $event) {
                $fields = $event->getParameter('fields');
                $result = new EventResult();
                if ('UF_FUS_TEST_BEFORE_ADD' === $fields['UF']['FIELD_NAME']) {
                    $result->addError(new EntityError('UF_FUS_TEST_BEFORE_ADD'));
                } elseif ('UF_FUS_TEST_BEFORE_ADD_S' === $fields['UF']['FIELD_NAME']) {
                    $result->modifyFields(['fields' => [
                        'UF' => [
                            'FIELD_NAME' => 'UF_FUS_TEST_BEFORE_ADD_SC'
                        ],
                    ]]);
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
}
