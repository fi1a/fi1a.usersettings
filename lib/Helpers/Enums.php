<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\Helpers;

use CUserFieldEnum;
use Fi1a\UserSettings\FieldInterface;
use InvalidArgumentException;

/**
 * Класс для работы с пользовательскими полями типа "список"
 */
class Enums
{
    /**
     * Возвращает список значений
     *
     * @return string[]
     */
    public static function get(FieldInterface $field)
    {
        if (!$field['UF_ID']) {
            throw new InvalidArgumentException('Пустой идентификатор пользовательского поля');
        }

        $enums = [];

        $iterator = CUserFieldEnum::GetList([], [
            'USER_FIELD_ID' => $field['UF_ID'],
        ]);

        while ($enum = $iterator->Fetch()) {
            $enums[] = $enum;
        }

        return $enums;
    }
}
