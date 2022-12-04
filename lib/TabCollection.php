<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Коллекция экземпляров классов вкладок пользовательских настроек
 */
class TabCollection extends AbstractBaseCollection implements ITabCollection
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
    protected function factory($key, $value)
    {
        return Tab::create($value);
    }

    /**
     * @inheritDoc
     */
    protected function isInstance($value): bool
    {
        return $value instanceof ITab;
    }
}
