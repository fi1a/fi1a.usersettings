<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Fi1a\UserSettings\SprintMigration\Builders\UserSettingsBuilder;

Loader::includeModule('sprint.migration');

$versionBuilders = \Sprint\Migration\VersionConfig::getDefaultBuilders();
$versionBuilders['UserSettings'] = UserSettingsBuilder::class;

return [
    'version_builders' => $versionBuilders,
];