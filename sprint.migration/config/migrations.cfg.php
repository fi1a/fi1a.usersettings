<?php

declare(strict_types=1);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Fi1a\UserSettings\SprintMigration\Builders\UserSettingsBuilder;

Loader::includeModule('sprint.migration');

$versionBuilders = \Sprint\Migration\VersionConfig::getDefaultBuilders();
$versionBuilders['UserSettings'] = UserSettingsBuilder::class;

return [
    'version_builders' => $versionBuilders,
];