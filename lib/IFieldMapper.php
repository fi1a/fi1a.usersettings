<?php

namespace Fi1a\UserSettings;

/**
 * Интерфейс маппера полей пользовательских настроек
 */
interface IFieldMapper
{

    /**
     * Возвращает список полей пользовательских настроек
     *
     * @param array $parameters - синтаксис d7
     *
     * @return IFieldCollection|IField[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getList(array $parameters = []): IFieldCollection;

    /**
     * Возвращает поле по идентификатору
     *
     * @param int $id
     *
     * @return bool|IField
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getById(int $id);

    /**
     * Возвращает список полей принадлежащих вкладке
     *
     * @param int $tabId
     *
     * @return IFieldCollection|bool|IField[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getByTabId(int $tabId);

    /**
     * Возвращает коллекцию с активными полями
     *
     * @param array $parameters
     *
     * @return IFieldCollection
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getActive(array $parameters = []): IFieldCollection;
}
