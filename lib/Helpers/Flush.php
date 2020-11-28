<?php

namespace Fi1a\UserSettings\Helpers;

/**
 * Хелпер
 */
class Flush
{

    /**
     * Установить значение
     *
     * @param string $key
     * @param mixed $valus
     *
     * @return bool
     */
    public static function set(string $key, $valus): bool
    {
        if (!$key) {
            return false;
        }

        $_SESSION[$key] = $valus;

        return true;
    }

    /**
     * Возвращает значение
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        $value = $_SESSION[$key];
        unset($_SESSION[$key]);

        return $value;
    }
}
