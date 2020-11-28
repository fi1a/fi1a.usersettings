<?php

namespace Fi1a\UserSettings\Collection;

/**
 * Интерфейс коллекции экземпляров классов
 */
interface IInstanceCollection extends ICollection
{

    /**
     * Возвращает экземпляр класса элемента коллекции
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     */
    public static function factory($key, $value);

    /**
     * Определяет является ли значение экземпляром класса элемента коллекции
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isInstance($value): bool;

    /**
     * Магический метод
     *
     * Пробрасывает вызов функции для каждого элемента и возвращает массив значений
     * результата выполнения этих методов или null, если такого метода нет
     *
     * @param string $func название функции
     * @param array  $args аргументы функции
     *
     * @return array
     */
    public function __call($func, $args);
}
