<?php

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
     * @return array a copy of the array. When the <b>ArrayObject</b> refers to an object
     *        an array of the public properties of that object will be returned.
     * @since 5.0.0
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
     * @return array the old array.
     * @since 5.1.0
     */
    public function exchangeArray($input);

    /**
     * Клонирование объекта
     *
     * @return static
     */
    public function getClone();
}
