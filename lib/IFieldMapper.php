<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Интерфейс маппера полей пользовательских настроек
 */
interface IFieldMapper
{
    /**
     * Возвращает список полей пользовательских настроек
     *
     * @param string[] $parameters - синтаксис d7
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
     * @param string[] $parameters
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getActive(array $parameters = []): IFieldCollection;

    /**
     * Возвращает поле по коду
     *
     * @return bool|IField
     */
    public static function getByCode(string $code);
}
