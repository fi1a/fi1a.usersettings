<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Коллекция экземпляров классов полей пользовательских настроек
 */
class FieldCollection extends AbstractBaseCollection implements IFieldCollection
{
    /**
     * Возвращает экземпляр класса элемента коллекции
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return FieldInterface
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function factory($key, $value)
    {
        return Field::create($value);
    }

    /**
     * @inheritDoc
     */
    protected function isInstance($value): bool
    {
        return $value instanceof Field;
    }
}
