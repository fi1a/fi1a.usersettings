<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\Events;

use Fi1a\UserSettings\SprintMigration\Helpers\UserSettingsHelper;
use Sprint\Migration\HelperManager;

/**
 * Обработчик событий модуля sprint.migration
 */
class SprintMigration
{
    /**
     * Обработчик при поиске файлов конфигураций модуля sprint.migration
     */
    public static function onSearchConfigFiles(): string
    {
        HelperManager::getInstance()->registerHelper('userSettings', UserSettingsHelper::class);

        return __DIR__ . '/../../sprint.migration.config';
    }
}
