<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\Collection;

/**
 * Интерфейс IArrayObject
 */
interface IArrayObject extends \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * Creates a copy of the ArrayObject.
     *
     * @link  http://php.net/manual/en/arrayobject.getarraycopy.php
     *
     * @return mixed[] a copy of the array. When the <b>ArrayObject</b> refers to an object
     *        an array of the public properties of that object will be returned.
     */
    public function getArrayCopy();

    /**
     * Exchange the array for another one.
     *
     * @link  http://php.net/manual/en/arrayobject.exchangearray.php
     *
     * @param mixed $input <p>
     *                     The new array or object to exchange with the current array.
     *                     </p>
     *
     * @return mixed[] the old array.
     */
    public function exchangeArray($input);

    /**
     * Клонирование объекта
     *
     * @return static
     */
    public function getClone();
}
