<?php

declare(strict_types=1);

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
     */
    public static function isInstance($value): bool;

    /**
     * Магический метод
     *
     * Пробрасывает вызов функции для каждого элемента и возвращает массив значений
     * результата выполнения этих методов или null, если такого метода нет
     *
     * @param string $func название функции
     * @param mixed[]  $args аргументы функции
     *
     * @return mixed[]
     */
    public function __call(string $func, array $args);
}
