<?php

namespace Fi1a\UserSettings\Collection;

/**
 * Интерфейс коллекции
 */
interface ICollection extends IArrayObject
{

    /**
     * Есть ли элемент с таким ключем
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key) : bool;

    /**
     * Возвращает элемент по ключу
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Устанавливает значение по ключу
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function set($key, $value);

    /**
     * Удаляет элемент по ключу
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function delete($key): bool;

    /**
     * Вызывает переданную функцию передавая ключ и значение из коллекции
     *
     * @param callable $callback
     *
     * @return static
     */
    public function each(callable $callback);

    /**
     * Вызывает переданную функцию передавая ключ и значение из коллекции и заменяет элемент результатом
     *
     * @param callable $callback
     *
     * @return static
     */
    public function map(callable $callback);
}
