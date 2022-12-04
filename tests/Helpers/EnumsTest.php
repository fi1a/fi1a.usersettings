<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings\Helpers;

use CUserFieldEnum;
use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\FieldInterface;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\Helpers\Enums;
use Fi1a\UserSettings\Tab;
use Fi1a\UserSettings\TabMapper;
use InvalidArgumentException;

/**
 * Класс для работы с пользовательскими полями типа "список"
 */
class EnumsTest extends ModuleTestCase
{
    /**
     * @var int|null
     */
    private static $tab;

    /**
     * @var int|null
     */
    private static $field;

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
        self::$tab = $result->getId();

        $field = Field::create([
            'TAB_ID' => self::$tab,
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD1',
                'USER_TYPE_ID' => 'enumeration',
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
        self::$field = $result->getId();

        $obEnum = new CUserFieldEnum();
        $result = $obEnum->SetEnumValues($field['UF_ID'], [
            'n0' => [
                'VALUE' => 'value1',
                'XML_ID' => 'XML_ID_1_ENUM',
            ],
            'n1' => [
                'VALUE' => 'value2',
                'XML_ID' => 'XML_ID_2_ENUM',
            ],
            'n2' => [
                'VALUE' => 'value3',
                'XML_ID' => 'XML_ID_3_ENUM',
            ],
        ]);
        if (!$result) {
            throw new \ErrorException();
        }
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        $field = FieldMapper::getById(self::$field);
        CUserFieldEnum::DeleteFieldEnum($field['UF_ID']);
        TabMapper::getById(self::$tab)->delete();
        parent::tearDownAfterClass();
    }

    /**
     * Возвращает список значений
     */
    public function testGet(): void
    {
        $field = FieldMapper::getById(self::$field);
        $this->assertInstanceOf(FieldInterface::class, $field);
        $enums = Enums::get($field);
        $this->assertIsArray($enums);
        $this->assertCount(3, $enums);
    }

    /**
     * Возвращает список значений
     */
    public function testGetException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $field = FieldMapper::getById(self::$field);
        $this->assertInstanceOf(FieldInterface::class, $field);
        $field['UF_ID'] = null;
        Enums::get($field);
    }
}
