<?php

namespace Fi1a\UserSettings\Collection;

/**
 * Класс ArrayObject
 */
class ArrayObject extends \ArrayObject implements IArrayObject
{

    /**
     * @inheritDoc
     */
    public function __construct($input = null, $flags = 0, $iteratorClass = \ArrayIterator::class)
    {
        parent::__construct((array)$input, $flags, $iteratorClass);
    }

    /**
     * Клонирование
     *
     * запрещаем клонирование из-за ошибки (ссылка в массиве)
     *
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * @inheritDoc
     */
    public function getClone()
    {
        return new static($this->getArrayCopy());
    }
}
