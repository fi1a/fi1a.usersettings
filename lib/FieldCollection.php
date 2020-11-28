<?php

namespace Fi1a\UserSettings;

/**
 * Коллекция экземпляров классов полей пользовательских настроек
 */
class FieldCollection extends ABaseCollection implements IFieldCollection
{

    /**
     * Возвращает экземпляр класса элемента коллекции
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return IField|mixed
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function factory($key, $value)
    {
        return Field::create($value);
    }

    /**
     * @inheritDoc
     */
    public static function isInstance($value): bool
    {
        return $value instanceof Field;
    }
}
