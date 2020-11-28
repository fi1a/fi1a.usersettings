<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\Result;

/**
 * Интерфейс класса реализующего работу со значениями пользовательских настроек
 */
interface IOption
{

    const ENTITY_ID = 'FUS';

    const ID = 1;

    const CACHE_ID = 'fus_option';

    /**
     * Синглетон
     *
     * @return IOption
     */
    public static function getInstance(): IOption;

    /**
     * Возвращает все значения
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Возвращает значение
     *
     * @param string $key
     * @param mixed|false $default
     *
     * @return mixed
     */
    public function get(string $key, $default = false);

    /**
     * Устанавливает значение
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function set(string $key, $value): Result;

    /**
     * Очистить кеш полей со значениями
     *
     * @return bool
     */
    public function clearCache(): bool;
}
