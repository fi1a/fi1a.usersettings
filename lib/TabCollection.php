<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Коллекция экземпляров классов вкладок пользовательских настроек
 */
class TabCollection extends ABaseCollection implements ITabCollection
{
    /**
     * Возвращает экземпляр класса элемента коллекции
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return ITab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function factory($key, $value)
    {
        return Tab::create($value);
    }

    /**
     * @inheritDoc
     */
    public static function isInstance($value): bool
    {
        return $value instanceof ITab;
    }
}
