<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Интерфейс маппера вкладок пользовательских настроек
 */
interface ITabMapper
{
    /**
     * Возвращает список вкладок пользовательских настроек
     *
     * @param string[] $parameters - синтаксис d7
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getList(array $parameters = []): TabCollectionInterface;

    /**
     * Возвращает вкладку по ее идентификатору
     *
     * @return bool|TabInterface
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getById(int $id);

    /**
     * Возвращает коллекцию с активными табами
     *
     * @param string[] $parameters
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getActive(array $parameters = []): TabCollectionInterface;
}
