<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Fi1a\Collection\InstanceCollectionInterface;

/**
 * Интерфейс коллекций вкладок и полей
 */
interface BaseCollectionInterface extends InstanceCollectionInterface
{
    /**
     * Преобразует экземпляры классов в массив
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
