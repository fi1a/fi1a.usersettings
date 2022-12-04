<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Fi1a\UserSettings\Internals\TabsTable;

/**
 * Маппер вкладок пользовательских настроек
 */
class TabMapper implements ITabMapper
{
    /**
     * @inheritDoc
     */
    public static function getList(array $parameters = []): TabCollectionInterface
    {
        $tabs = new TabCollection();

        $tabsIterator = TabsTable::getList($parameters);

        while ($tab = $tabsIterator->fetch()) {
            $tabs[$tab['ID']] = $tab;
        }

        return $tabs;
    }

    /**
     * @inheritDoc
     */
    public static function getById(int $id)
    {
        if (!$id) {
            return false;
        }

        $collection = static::getList(['filter' => ['ID' => $id,],]);

        if (!count($collection)) {
            return false;
        }

        $collectionArray = $collection->getArrayCopy();

        return reset($collectionArray);
    }

    /**
     * @inheritDoc
     */
    public static function getActive(array $parameters = []): TabCollectionInterface
    {
        $parameters['filter']['ACTIVE'] = 1;

        return static::getList($parameters);
    }
}
