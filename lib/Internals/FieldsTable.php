<?php

namespace Fi1a\UserSettings\Internals;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Validators\ForeignValidator;
use Bitrix\Main\UserFieldTable;
use Fi1a\UserSettings\Option;

Loc::loadMessages(__FILE__);

/**
 * Поля
 */
class FieldsTable extends DataManager
{

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'fus_fields';
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
            'TAB_ID' => new IntegerField('TAB_ID', [
                'required' => true,
                'validation' => function() {
                    return [
                        new ForeignValidator(TabsTable::getEntity()->getField('ID')),
                    ];
                },
            ]),
            'UF_ID' => new IntegerField('UF_ID', [
                'required' => true,
                'validation' => function() {
                    return [
                        new ForeignValidator(
                            UserFieldTable::getEntity()->getField('ID'),
                            ['ENTITY_ID' => Option::ENTITY_ID]
                        ),
                    ];
                },
            ]),
        ];
    }
}
