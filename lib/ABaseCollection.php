<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Fi1a\UserSettings\Collection\AInstanceCollection;

/**
 * Абстрактный класс коллекций вкладок и полей
 */
abstract class ABaseCollection extends AInstanceCollection implements IBaseCollection
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this as $object) {
            $array[] = $object->getArrayCopy();
        }

        return $array;
    }
}
