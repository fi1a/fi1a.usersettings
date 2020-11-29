<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\Result;

/**
 * Интерфейс класса реализующего работу со значениями пользовательских настроек
 */
interface IOption
{
    public const ENTITY_ID = 'FUS';

    public const ID = 1;

    public const CACHE_ID = 'fus_option';

    /**
     * Синглетон
     */
    public static function getInstance(): IOption;

    /**
     * Возвращает все значения
     *
     * @return string[]
     */
    public function getAll(): array;

    /**
     * Возвращает значение
     *
     * @param mixed|false $default
     *
     * @return mixed
     */
    public function get(string $key, $default = false);

    /**
     * Устанавливает значение
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function set(string $key, $value): Result;

    /**
     * Очистить кеш полей со значениями
     */
    public function clearCache(): bool;
}
