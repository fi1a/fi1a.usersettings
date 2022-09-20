<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\SprintMigration\Builders;

use Bitrix\Main\Localization\Loc;
use Sprint\Migration\VersionBuilder;

/**
 * Менеджер модуля пользовательских настроек
 */
class UserSettingsBuilder extends VersionBuilder
{
    /**
     * Активность
     */
    protected function isBuilderEnabled(): bool
    {
        return true;
    }

    /**
     * Инициализация
     */
    protected function initialize(): void
    {
        $this->setTitle(Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_TITLE'));
        $this->setGroup('Tools');

        $this->addVersionFields();
    }

    /**
     * Выполнение
     */
    protected function execute(): void
    {
    }
}
