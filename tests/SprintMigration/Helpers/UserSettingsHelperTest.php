<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings\SprintMigration\Helpers;

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult;
use CUserFieldEnum;
use CUserTypeEntity;
use Fi1a\Unit\UserSettings\SprintMigration\Helpers\Fixtures\FixtureUserSettingsHelper;
use Fi1a\Unit\UserSettings\TestCase\TabsAndFieldsTestCase;
use Fi1a\UserSettings\Field;
use Fi1a\UserSettings\IOption;
use InvalidArgumentException;
use Sprint\Migration\Exceptions\HelperException;

use const PHP_INT_MAX;

/**
 * Тестирование маппера вкладок
 */
class UserSettingsHelperTest extends TabsAndFieldsTestCase
{
    /**
     * @var int
     */
    private static $tab1Id;

    /**
     * @var int
     */
    private static $field1Id;

    /**
     * Выполнить до теста
     *
     * @throws \Bitrix\Main\LoaderException
     */
    protected function setUp(): void
    {
        if (!Loader::includeModule('sprint.migration')) {
            $this->markTestSkipped('sprint.migration module is not installed');
        }

        parent::setUp();
    }

    /**
     * Тестирование getList
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetList(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $array = $helper->getTabs([
            'CODE' => 'FUS_TEST_TAB1',
        ]);
        $this->assertIsArray($array);
        $this->assertCount(1, $array);
    }

    /**
     * Тестирование getTabId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testTabId(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $id = $helper->getTabId('FUS_TEST_TAB1');
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $id);
        $this->assertFalse($helper->getTabId('FUS_TEST_TAB_UNKNOWN'));
        $this->expectException(InvalidArgumentException::class);
        $helper->getTabId('');
    }

    /**
     * Тестирование getTabIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testTabIdIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $id = $helper->getTabIdIfExists('FUS_TEST_TAB1');
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $id);
        $this->expectException(HelperException::class);
        $helper->getTabIdIfExists('FUS_TEST_TAB_UNKNOWN');
    }

    /**
     * Тестирование getTabIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testTabIdIfExistsEmptyCode(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $this->expectException(InvalidArgumentException::class);
        $helper->getTabIdIfExists('');
    }

    /**
     * Тестирование getTabByCode
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetTabByCode(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $tab = $helper->getTabByCode('FUS_TEST_TAB1');
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $tab['ID']);
        $this->assertFalse($helper->getTabByCode('FUS_TEST_TAB_UNKNOWN'));
        $this->expectException(InvalidArgumentException::class);
        $helper->getTabByCode('');
    }

    /**
     * Тестирование getTabByCodeIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetTabByCodeIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $tab = $helper->getTabByCodeIfExists('FUS_TEST_TAB1');
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $tab['ID']);
        $this->expectException(HelperException::class);
        $helper->getTabByCodeIfExists('FUS_TEST_TAB_UNKNOWN');
    }

    /**
     * Тестирование getTabById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetTabById(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $tab = $helper->getTabById(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $tab['ID']);
        $this->assertFalse($helper->getTabById(PHP_INT_MAX));
        $this->expectException(InvalidArgumentException::class);
        $helper->getTabById(0);
    }

    /**
     * Тестирование getTabByIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetTabByIdIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $tab = $helper->getTabByIdIfExists(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertEquals(self::$tabIds['FUS_TEST_TAB1'], $tab['ID']);
        $this->expectException(HelperException::class);
        $helper->getTabByIdIfExists(PHP_INT_MAX);
    }

    /**
     * Тестирование getTabByIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetTabByIdIfExistsEmptyId(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $this->expectException(InvalidArgumentException::class);
        $helper->getTabByIdIfExists(0);
    }

    /**
     * Тестирование exportTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testExportTab(): void
    {
        $helper = new FixtureUserSettingsHelper();
        $tab = $helper->exportTab(self::$tabIds['FUS_TEST_TAB1']);
        $this->assertEquals('FUS_TEST_TAB1', $tab['CODE']);
        $this->assertFalse($helper->exportTab(PHP_INT_MAX));
        $this->expectException(InvalidArgumentException::class);
        $helper->exportTab(0);
    }

    /**
     * Тестирование метода addTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAddTab(): void
    {
        $helper = new FixtureUserSettingsHelper();

        static::$tab1Id = $helper->addTab('FUS_HELPER_TEST_TAB1', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]);
        $this->assertNotNull(static::$tab1Id);
        $this->expectException(InvalidArgumentException::class);
        $helper->addTab('', []);
    }

    /**
     * Тестирование метода addTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testAddTabException(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->addTab('FUS_HELPER_TEST_TAB1', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]);
    }

    /**
     * Тестирование метода addTabIfNotExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testAddTabIfNotExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $tab1Id = $helper->addTabIfNotExists('FUS_HELPER_TEST_TAB1', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]);
        $this->assertEquals(static::$tab1Id, $tab1Id);
        $tab2Id = $helper->addTabIfNotExists('FUS_HELPER_TEST_TAB2', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]);
        $this->assertNotNull($tab2Id);
        $this->expectException(InvalidArgumentException::class);
        $helper->addTabIfNotExists('', []);
    }

    /**
     * Тестирование метода updateTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testUpdateTab(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertEquals(static::$tab1Id, $helper->updateTab(static::$tab1Id, ['ACTIVE' => 0]));
        $this->expectException(InvalidArgumentException::class);
        $helper->updateTab(0, ['ACTIVE' => 0]);
    }

    /**
     * Тестирование метода updateTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testUpdateTabNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->updateTab(static::$tab1Id, ['ACTIVE' => null]);
    }

    /**
     * Тестирование метода updateTabIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testUpdateTabIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertFalse($helper->updateTabIfExists(PHP_INT_MAX, ['ACTIVE' => 0]));
        $this->assertEquals(static::$tab1Id, $helper->updateTabIfExists(static::$tab1Id, ['ACTIVE' => 0]));
        $this->expectException(InvalidArgumentException::class);
        $helper->updateTabIfExists(0, ['ACTIVE' => 0]);
    }

    /**
     * Тестирование метода saveTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testSaveTab(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertNotNull($helper->saveTab('FUS_HELPER_TEST_TAB3', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]));
        $this->assertNotNull($helper->saveTab('FUS_HELPER_TEST_TAB1', [
            'ACTIVE' => 1,
            'LOCALIZATION' => [
                'ru' => [
                    'L_NAME' => 'name',
                    'L_TITLE' => 'title',
                ],
            ],
        ]));
        $this->expectException(InvalidArgumentException::class);
        $helper->saveTab('', ['ACTIVE' => 0]);
    }

    /**
     * Тестирование метода deleteTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testAddTab
     */
    public function testDeleteTab(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertTrue($helper->deleteTab('FUS_HELPER_TEST_TAB1'));
        $this->expectException(InvalidArgumentException::class);
        $helper->deleteTab('');
    }

    /**
     * Тестирование метода deleteTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testDeleteTab
     */
    public function testDeleteTabNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->deleteTab('FUS_HELPER_TEST_TAB1');
    }

    /**
     * Тестирование метода deleteTab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testDeleteTab
     */
    public function testDeleteTabEventError(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeTabDelete',
            function (Event $event) {
                $result = new EventResult();
                $result->addError(new EntityError('UF_FUS_TEST_BEFORE_DELETE'));

                return $result;
            }
        );
        $this->expectException(HelperException::class);
        try {
            $helper->deleteTab('FUS_HELPER_TEST_TAB2');
        } catch (HelperException $exception) {
            EventManager::getInstance()->removeEventHandler(
                self::MODULE_ID,
                'OnBeforeTabDelete',
                $eventHandlerKey
            );

            throw $exception;
        }
    }

    /**
     * Тестирование метода deleteTabIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     *
     * @depends testDeleteTab
     */
    public function testDeleteTabIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertFalse($helper->deleteTabIfExists('FUS_HELPER_TEST_TAB1'));
        $this->assertTrue($helper->deleteTabIfExists('FUS_HELPER_TEST_TAB2'));
        $this->expectException(InvalidArgumentException::class);
        $helper->deleteTabIfExists('');
    }

    /**
     * Тестирование метода getFields
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFields(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertCount(2, $helper->getFields(['TAB_ID' => self::$tabIds['FUS_TEST_TAB1']]));
    }

    /**
     * Тестирование метода getFieldByCode
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByCode(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertIsArray($helper->getFieldByCode('UF_FUS_TEST_FIELD1'));
        $this->assertFalse($helper->getFieldByCode('UF_FUS_TEST_UNKNOWN'));
        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldByCode('');
    }

    /**
     * Тестирование метода getFieldByCode
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByCodeUfFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $userTypeEntity  = new CUserTypeEntity();
        $userTypeId = $userTypeEntity->Add([
            'ENTITY_ID' => IOption::ENTITY_ID,
            'FIELD_NAME' => 'UF_FUS_TEST_FIELD_CHECK',
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
        ]);
        $this->assertIsInt($userTypeId);
        $this->assertFalse($helper->getFieldByCode('UF_FUS_TEST_FIELD_CHECK'));
        $userTypeEntity->Delete($userTypeId);
    }

    /**
     * Тестирование метода getFieldByCode
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByCodeEnumeration(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $field = Field::create([
            'TAB_ID' => self::$tabIds['FUS_TEST_TAB1'],
            'ACTIVE' => 1,
            'UF' => [
                'FIELD_NAME' => 'UF_FUS_TEST_FIELD3',
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
        static::$field1Id = $result->getId();
        $this->assertIsInt(static::$field1Id);
        $data = $result->getData();
        $this->assertIsInt($data['UF_ID']);

        $this->assertTrue((new CUserFieldEnum())->SetEnumValues($data['UF_ID'], [
            'n0' => [
                'VALUE' => 1,
                'XML_ID'  => 'XML_ID_1',
            ],
            'n1' => [
                'VALUE' => 2,
                'XML_ID'  => 'XML_ID_2',
            ],
        ]));

        $fieldByCode = $helper->getFieldByCode('UF_FUS_TEST_FIELD3');
        $this->assertIsArray($fieldByCode);
        $this->assertCount(2, $fieldByCode['UF']['ENUMS']);
    }

    /**
     * Тестирование метода getFieldByCodeIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByCodeIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertIsArray($helper->getFieldByCodeIfExists('UF_FUS_TEST_FIELD1'));

        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldByCodeIfExists('');
    }

    /**
     * Тестирование метода getFieldByCodeIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByCodeIfExistsNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->getFieldByCodeIfExists('UF_FUS_TEST_UNKNOWN');
    }

    /**
     * Тестирование метода getFieldId
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldId(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertIsInt($helper->getFieldId('UF_FUS_TEST_FIELD1'));
        $this->assertFalse($helper->getFieldId('UF_FUS_TEST_UNKNOWN'));
        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldId('');
    }

    /**
     * Тестирование метода getFieldIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldIdIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertIsInt($helper->getFieldIdIfExists('UF_FUS_TEST_FIELD1'));
        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldIdIfExists('');
    }

    /**
     * Тестирование метода getFieldIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldIdIfExistsNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->getFieldIdIfExists('UF_FUS_TEST_UNKNOWN');
    }

    /**
     * Тестирование метода getFieldById
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldById(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $field = $helper->getFieldById(static::$field1Id);
        $this->assertIsArray($field);
        $this->assertCount(2, $field['UF']['ENUMS']);
        $this->assertFalse($helper->getFieldById(PHP_INT_MAX));
        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldById(0);
    }

    /**
     * Тестирование метода getFieldByIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByIdIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $field = $helper->getFieldByIdIfExists(static::$field1Id);
        $this->assertIsArray($field);
        $this->assertCount(2, $field['UF']['ENUMS']);
        $this->expectException(InvalidArgumentException::class);
        $helper->getFieldByIdIfExists(0);
    }

    /**
     * Тестирование метода getFieldByIdIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetFieldByIdIfExistsNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->getFieldByIdIfExists(PHP_INT_MAX);
    }

    /**
     * Тестирование метода exportField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testExportField(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $field = $helper->exportField(static::$field1Id);
        $this->assertIsArray($field);
        $this->assertCount(2, $field['UF']['ENUMS']);
        $this->expectException(InvalidArgumentException::class);
        $helper->exportField(0);
    }

    /**
     * Тестирование метода addField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAddField(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $fieldId = $helper->addField('UF_FUS_TEST_FIELD4', [
            'TAB_CODE' => 'FUS_TEST_TAB1',
            'ACTIVE' => 1,
            'UF' => [
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
                'ENUMS' => [
                    [
                        'VALUE' => 1,
                        'XML_ID'  => 'XML_ID_1',
                    ],
                    [
                        'VALUE' => 2,
                        'XML_ID'  => 'XML_ID_2',
                    ],
                    [
                        'VALUE' => 3,
                        'XML_ID'  => 'XML_ID_3',
                    ],
                ],
            ],
        ]);

        $this->assertIsInt($fieldId);
        $field = $helper->getFieldById($fieldId);
        $this->assertIsArray($field);
        $this->assertCount(3, $field['UF']['ENUMS']);
        $this->expectException(InvalidArgumentException::class);
        $helper->addField('', []);
    }

    /**
     * Тестирование метода addField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAddFieldError(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->addField('UF_FUS_TEST_FIELD5', [
            'TAB_CODE' => 'FUS_TEST_TAB1',
            'ACTIVE' => 1,
            'UF' => [
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
    }

    /**
     * Тестирование метода addFieldIfNotExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testAddFieldIfNotExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $fieldId = $helper->addFieldIfNotExists('UF_FUS_TEST_FIELD5', [
            'TAB_CODE' => 'FUS_TEST_TAB1',
            'ACTIVE' => 1,
            'UF' => [
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
                'ENUMS' => [
                    [
                        'VALUE' => 1,
                        'XML_ID'  => 'XML_ID_1',
                    ],
                    [
                        'VALUE' => 2,
                        'XML_ID'  => 'XML_ID_2',
                    ],
                    [
                        'VALUE' => 3,
                        'XML_ID'  => 'XML_ID_3',
                    ],
                ],
            ],
        ]);

        $this->assertIsInt($fieldId);
        $field = $helper->getFieldById($fieldId);
        $this->assertIsArray($field);
        $this->assertCount(3, $field['UF']['ENUMS']);

        $this->assertEquals(static::$field1Id, $helper->addFieldIfNotExists('UF_FUS_TEST_FIELD3', [
            'TAB_CODE' => 'FUS_TEST_TAB1',
            'ACTIVE' => 1,
            'UF' => [
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
        ]));
        $this->expectException(InvalidArgumentException::class);
        $helper->addFieldIfNotExists('', []);
    }

    /**
     * Тестирование метода updateField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testUpdateField(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $field = $helper->getFieldById(static::$field1Id);
        $this->assertIsArray($field);
        $this->assertCount(2, $field['UF']['ENUMS']);

        $helper->updateField(static::$field1Id, [
            'ACTIVE' => 0,
            'TAB_CODE' => 'FUS_TEST_TAB1',
            'UF' => [
                'ENUMS' => [
                    [
                        'VALUE' => 1,
                        'XML_ID'  => 'XML_ID_1',
                    ],
                    [
                        'VALUE' => 2,
                        'XML_ID'  => 'XML_ID_2',
                    ],
                    [
                        'VALUE' => 3,
                        'XML_ID'  => 'XML_ID_3',
                    ],
                ],
            ],
        ]);

        $field = $helper->getFieldById(static::$field1Id);
        $this->assertIsArray($field);
        $this->assertCount(3, $field['UF']['ENUMS']);

        $this->expectException(InvalidArgumentException::class);
        $helper->updateField(0, []);
    }

    /**
     * Тестирование метода updateField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testUpdateFieldNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->updateField(PHP_INT_MAX, [
            'ACTIVE' => 0,
        ]);
    }

    /**
     * Тестирование метода updateField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testUpdateFieldError(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->updateField(static::$field1Id, [
            'ACTIVE' => 0,
            'TAB_CODE' => 'FUS_TEST_TAB_UNKNOWN',
        ]);
    }

    /**
     * Тестирование метода updateFieldIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testUpdateFieldIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertFalse($helper->updateFieldIfExists(PHP_INT_MAX, ['ACTIVE' => 0]));
        $this->assertIsInt($helper->updateFieldIfExists(static::$field1Id, [
            'ACTIVE' => 0,
        ]));
        $this->expectException(InvalidArgumentException::class);
        $helper->updateFieldIfExists(0, ['ACTIVE' => 0,]);
    }

    /**
     * Тестирование метода saveField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testSaveField(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $fieldId = $helper->saveField('UF_FUS_TEST_FIELD6', [
            'TAB_CODE' => 'FUS_TEST_TAB1',
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
        $this->assertIsInt($fieldId);

        $fieldId = $helper->saveField('UF_FUS_TEST_FIELD6', [
            'ACTIVE' => 0,
        ]);
        $this->assertIsInt($fieldId);

        $this->expectException(InvalidArgumentException::class);
        $helper->saveField('', ['ACTIVE' => 0,]);
    }

    /**
     * Тестирование метода deleteField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testDeleteField(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertTrue($helper->deleteField('UF_FUS_TEST_FIELD6'));

        $this->expectException(InvalidArgumentException::class);
        $helper->deleteField('');
    }

    /**
     * Тестирование метода deleteField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testDeleteFieldNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->deleteField('UF_FUS_TEST_FIELD6');
    }

    /**
     * Тестирование метода deleteField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testDeleteFieldError(): void
    {
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeFieldDelete',
            function (Event $event) {
                throw new \ErrorException();
            }
        );

        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        try {
            $helper->deleteField('UF_FUS_TEST_FIELD5');
        } catch (HelperException $exception) {
            EventManager::getInstance()->removeEventHandler(
                self::MODULE_ID,
                'OnBeforeFieldDelete',
                $eventHandlerKey
            );

            throw $exception;
        }
    }

    /**
     * Тестирование метода deleteFieldIfExists
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testDeleteFieldIfExists(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertTrue($helper->deleteFieldIfExists('UF_FUS_TEST_FIELD5'));
        $this->assertFalse($helper->deleteFieldIfExists('UF_FUS_TEST_FIELD5'));

        $this->expectException(InvalidArgumentException::class);
        $helper->deleteFieldIfExists('');
    }

    /**
     * Тестирование метода setOption & getOption
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testOptions(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->assertFalse($helper->getOption('UF_FUS_TEST_FIELD3'));
        $this->assertTrue($helper->setOption('UF_FUS_TEST_FIELD3', 'XML_ID_1'));
        $this->assertEquals('XML_ID_1', $helper->getOption('UF_FUS_TEST_FIELD3'));
    }

    /**
     * Тестирование метода setOption
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testSetOptionNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->setOption('UF_FUS_TEST_FIELD_UNKNOWN', 'XML_ID_1');
    }

    /**
     * Тестирование метода setOption
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testSetOptionError(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $eventHandlerKey = EventManager::getInstance()->addEventHandler(
            self::MODULE_ID,
            'OnBeforeOptionSet',
            function (Event $event) {
                throw new \ErrorException();
            }
        );
        try {
            $helper->setOption('UF_FUS_TEST_FIELD3', 'XML_ID_2');
        } catch (HelperException $exception) {
            EventManager::getInstance()->removeEventHandler(
                self::MODULE_ID,
                'OnBeforeOptionSet',
                $eventHandlerKey
            );

            throw $exception;
        }
    }

    /**
     * Тестирование метода getOption
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function testGetOptionNotFound(): void
    {
        $helper = new FixtureUserSettingsHelper();

        $this->expectException(HelperException::class);
        $helper->getOption('UF_FUS_TEST_FIELD_UNKNOWN');
    }
}
