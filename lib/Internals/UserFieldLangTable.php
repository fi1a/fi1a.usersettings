<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\Internals;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;

/**
 * Языковые сообщения пользовательских полей
 *
 * @codeCoverageIgnore
 */
class UserFieldLangTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_user_field_lang';
    }

    /**
     * Returns entity map definition.
     *
     * @return mixed[]
     */
    public static function getMap()
    {
        return [
            'LANGUAGE_ID' => new TextField('LANGUAGE_ID', [
                'primary' => true,
            ]),
            'USER_FIELD_ID' => new IntegerField('USER_FIELD_ID', [
                'primary' => true,
            ]),
            'EDIT_FORM_LABEL' => new TextField('EDIT_FORM_LABEL', [
                'required' => true,
            ]),
            'HELP_MESSAGE' => new TextField('HELP_MESSAGE', [
                'required' => true,
            ]),
        ];
    }

    /**
     * @param string[] $data
     *
     * @return \Bitrix\Main\ORM\Data\AddResult|void
     *
     * @throws NotImplementedException
     */
    public static function add(array $data)
    {
        throw new NotImplementedException('Use \CUserTypeEntity API instead.');
    }

    /**
     * @param mixed $primary
     * @param string[] $data
     *
     * @return \Bitrix\Main\ORM\Data\UpdateResult|void
     *
     * @throws NotImplementedException
     */
    public static function update($primary, array $data)
    {
        throw new NotImplementedException('Use \CUserTypeEntity API instead.');
    }

    /**
     * @param mixed $primary
     *
     * @return \Bitrix\Main\ORM\Data\void
     *
     * @throws NotImplementedException
     */
    public static function delete($primary)
    {
        throw new NotImplementedException('Use \CUserTypeEntity API instead.');
    }
}
