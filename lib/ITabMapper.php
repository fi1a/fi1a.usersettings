<?php

namespace Fi1a\UserSettings;

/**
 * Интерфейс маппера вкладок пользовательских настроек
 */
interface ITabMapper
{

    /**
     * Возвращает список вкладок пользовательских настроек
     *
     * @param array $parameters - синтаксис d7
     *
     * @return ITabCollection
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getList(array $parameters = []): ITabCollection;

    /**
     * Возвращает вкладку по ее идентификатору
     *
     * @param int $id
     *
     * @return bool|ITab
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getById(int $id);

    /**
     * Возвращает коллекцию с активными табами
     *
     * @param array $parameters
     *
     * @return ITabCollection
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getActive(array $parameters = []): ITabCollection;
}
