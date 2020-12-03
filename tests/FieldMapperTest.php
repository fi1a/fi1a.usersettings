<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\IField;
use Fi1a\UserSettings\IFieldCollection;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabMapper;

use const PHP_INT_MAX;

class FieldMapperTest extends ModuleTestCase
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
        $result = $field->save();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$fieldIds['UF_FUS_TEST_FIELD1'] = $result->getId();
        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
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
        $result = $field->save();
        if (!$result->isSuccess()) {
            throw new \ErrorException();
        }
        self::$fieldIds['UF_FUS_TEST_FIELD2'] = $result->getId();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        foreach (self::$fieldIds as $fieldId) {
            FieldMapper::getById($fieldId)->delete();
        }
        foreach (self::$tabIds as $tabId) {
            TabMapper::getById($tabId)->delete();
        }
        parent::tearDownAfterClass();
    }

    /**
     * Тестирование метода getList
     */
    public function testGetList(): void
    {
        $collection = FieldMapper::getList();
        $this->assertInstanceOf(IFieldCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    /**
     * Тестирование метода getById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetById(): void
    {
        $this->assertFalse(FieldMapper::getById(0));
        $this->assertFalse(FieldMapper::getById(PHP_INT_MAX));
        $this->assertInstanceOf(IField::class, FieldMapper::getById(self::$fieldIds['UF_FUS_TEST_FIELD1']));
    }

    /**
     * Тестирование метода getByTabId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetByTabId(): void
    {
        $this->assertFalse(FieldMapper::getByTabId(0));
        $collection = FieldMapper::getByTabId(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertInstanceOf(IFieldCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    /**
     * Тестирование метода getActive
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetActive(): void
    {
        $collection = FieldMapper::getActive();
        $this->assertInstanceOf(IFieldCollection::class, $collection);
        $this->assertCount(2, $collection);
    }
}
