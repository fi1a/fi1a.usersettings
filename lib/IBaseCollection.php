<?php

namespace Fi1a\UserSettings;

use Fi1a\UserSettings\Collection\IInstanceCollection;

/**
 * Интерфейс коллекций вкладок и полей
 */
interface IBaseCollection extends IInstanceCollection
{

    /**
     * Преобразует экземпляры классов в массив
     *
     * @return array
     */
    public function toArray(): array;
}
