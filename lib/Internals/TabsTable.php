<?php

namespace Fi1a\UserSettings\Internals;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\Validators\UniqueValidator;
use Bitrix\Main\ORM\Fields\Validators\RegExpValidator;

Loc::loadMessages(__FILE__);

/**
 * Вкладки
 */
class TabsTable extends DataManager
{

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'fus_tabs';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            'ACTIVE' => new BooleanField('ACTIVE', [
                'values' => [0, 1],
            ]),
            'CODE' => new TextField('CODE', [
                'required' => true,
                'validation' => function() {
                    return [
                        new RegExpValidator('/^[0-9A-Za-z_]+$/', Loc::getMessage('FUS_CODE_FORMAT_ERROR')),
                        new UniqueValidator(Loc::getMessage('FUS_CODE_UNIQUE_ERROR')),
                    ];
                }
            ]),
            'SORT' => new IntegerField('SORT', [
                'default_value' => 500,
            ]),
            'LOCALIZATION' => new TextField('LOCALIZATION', [
                'serialized' => true,
            ]),
        ];
    }
}
