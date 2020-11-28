<?php

namespace Fi1a\UserSettings\Collection;

/**
 * Абстрактный класс коллекции экземпляров классов
 */
abstract class AInstanceCollection extends Collection implements IInstanceCollection
{

    /**
     * @param null|array $input         массив со значениями
     * @param int        $flags         флаги
     * @param string     $iteratorClass класс итератора
     */
    public function __construct($input = null, $flags = 0, $iteratorClass = \ArrayIterator::class)
    {
        parent::__construct([], $flags, $iteratorClass);
        if (!is_array($input)) {
            return;
        }
        foreach ($input as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function __call($func, $args)
    {
        $result = [];
        foreach ($this as $item) {
            if (!method_exists($item, $func)) {
                $result[] = null;

                continue;
            }
            $result[] = call_user_func_array([$item, $func], $args);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($key, $value)
    {
        if (!is_object($value) || !static::isInstance($value)) {
            $value = static::factory($key, $value);
        }

        parent::offsetSet($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function getClone()
    {
        $collection = new static();

        foreach ($this as $key => $value) {
            $collection[$key] = clone $value;
        }

        return $collection;
    }
}
