<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

/**
 * Интерфейс маппера полей пользовательских настроек
 */
interface FieldMapperInterface
{
    /**
     * Возвращает список полей пользовательских настроек
     *
     * @param string[] $parameters - синтаксис d7
     *
     * @return FieldCollectionInterface|FieldInterface[]
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getList(array $parameters = []): FieldCollectionInterface;

    /**
     * Возвращает поле по идентификатору
     *
     * @return bool|FieldInterface
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getById(int $id);

    /**
     * Возвращает список полей принадлежащих вкладке
     *
     * @return FieldCollectionInterface|bool|FieldInterface[]
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
    public static function getActive(array $parameters = []): FieldCollectionInterface;

    /**
     * Возвращает поле по коду
     *
     * @return bool|FieldInterface
     */
    public static function getByCode(string $code);
}
