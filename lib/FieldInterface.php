<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Fi1a\Collection\DataType\ArrayObjectInterface;

/**
 * Интерфейс поля
 */
interface FieldInterface extends ArrayObjectInterface
{
    /**
     * Фабричный метод
     *
     * @param string[] $input
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function create(array $input = []): FieldInterface;

    /**
     * Сохранение
     *
     * @return AddResult|UpdateResult
     *
     * @throws \Exception
     */
    public function save();

    /**
     * Добавление
     *
     * @throws \Exception
     */
    public function add(): AddResult;

    /**
     * Обновление
     *
     * @throws \Exception
     */
    public function update(): UpdateResult;

    /**
     * Удаление
     *
     * @throws \Exception
     */
    public function delete(): DeleteResult;
}
