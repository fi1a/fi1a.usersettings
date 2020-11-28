<?php

namespace Fi1a\UserSettings\Collection;

/**
 * Коллекция
 */
class Collection extends ArrayObject implements ICollection
{

    /**
     * Есть ли элемент с таким ключем
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Возвращает элемент по ключу
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this[$key];
    }

    /**
     * Устанавливает значение по ключу
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this[$key] = $value;
    }

    /**
     * Удаляет элемент по ключу
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function delete($key): bool
    {
        if (!$this->has($key)) {
            return false;
        }
        unset($this[$key]);

        return true;
    }

    /**
     * Вызывает переданную функцию передавая ключ и значение из коллеекции
     *
     * @param callable $callback
     *
     * @return static
     */
    public function each(callable $callback)
    {
        foreach ($this as $ind => $value) {
            call_user_func($callback, $ind, $value);
        }

        return $this;
    }

    /**
     * Вызывает переданную функцию передавая ключ и значение из коллекции и заменяет элемент результатом
     *
     * @param callable $callback
     *
     * @return static
     */
    public function map(callable $callback)
    {
        foreach ($this as $ind => $value) {
            $this[$ind] = call_user_func($callback, $ind, $value);
        }

        return $this;
    }
}
