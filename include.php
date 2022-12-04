<?php
namespace Fi1a\UserSettings;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Fi1a\UserSettings\Helpers\ModuleRegistry;

if (is_file(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

$classLocFilePaths = [
    __DIR__ . '/lib/Tab.php',
    __DIR__ . '/lib/Field.php',
    __DIR__ . '/lib/Internals/TabsTable.php',
    __DIR__ . '/lib/Option.php',
    __DIR__ . '/lib/SprintMigration/Builders/UserSettingsBuilder.php',
    __DIR__ . '/lib/SprintMigration/Helpers/UserSettingsHelper.php',
];

foreach ($classLocFilePaths as $classLocFilePath) {
    Loc::loadMessages($classLocFilePath);
}

Loader::registerAutoloadClasses(
    'fi1a.usersettings',
    [
        // Хелперы
        '\Fi1a\UserSettings\Helpers\Flush' => 'lib/Helpers/Flush.php',
        '\Fi1a\UserSettings\Helpers\ModuleRegistry' => 'lib/Helpers/ModuleRegistry.php',

        // Коллекции
        '\Fi1a\UserSettings\Collection\IArrayObject' => 'lib/Collection/IArrayObject.php',
        '\Fi1a\UserSettings\Collection\ICollection' => 'lib/Collection/ICollection.php',
        '\Fi1a\UserSettings\Collection\IInstanceCollection' => 'lib/Collection/IInstanceCollection.php',
        '\Fi1a\UserSettings\Collection\ArrayObject' => 'lib/Collection/ArrayObject.php',
        '\Fi1a\UserSettings\Collection\Collection' => 'lib/Collection/Collection.php',
        '\Fi1a\UserSettings\Collection\AInstanceCollection' => 'lib/Collection/AInstanceCollection.php',

        // Классы ORM
        '\Fi1a\UserSettings\Internals\UserFieldLangTable' => 'lib/Internals/UserFieldLangTable.php',
        '\Fi1a\UserSettings\Internals\TabsTable' => 'lib/Internals/TabsTable.php',
        '\Fi1a\UserSettings\Internals\FieldsTable' => 'lib/Internals/FieldsTable.php',

        // Классы модуля
        '\Fi1a\UserSettings\UserTypeManager' => 'lib/UserTypeManager.php',
        '\Fi1a\UserSettings\TabInterface' => 'lib/TabInterface.php',
        '\Fi1a\UserSettings\Tab' => 'lib/Tab.php',
        '\Fi1a\UserSettings\FieldInterface' => 'lib/FieldInterface.php',
        '\Fi1a\UserSettings\Field' => 'lib/Field.php',
        '\Fi1a\UserSettings\BaseCollectionInterface' => 'lib/BaseCollectionInterface.php',
        '\Fi1a\UserSettings\AbstractBaseCollection' => 'lib/AbstractBaseCollection.php',
        '\Fi1a\UserSettings\TabCollectionInterface' => 'lib/TabCollectionInterface.php',
        '\Fi1a\UserSettings\TabCollection' => 'lib/TabCollection.php',
        '\Fi1a\UserSettings\TabMapperInterface' => 'lib/TabMapperInterface.php',
        '\Fi1a\UserSettings\TabMapper' => 'lib/TabMapper.php',
        '\Fi1a\UserSettings\FieldCollectionInterface' => 'lib/FieldCollectionInterface.php',
        '\Fi1a\UserSettings\FieldCollection' => 'lib/FieldCollection.php',
        '\Fi1a\UserSettings\FieldMapperInterface' => 'lib/FieldMapperInterface.php',
        '\Fi1a\UserSettings\FieldMapper' => 'lib/FieldMapper.php',
        '\Fi1a\UserSettings\OptionInterface' => 'lib/OptionInterface.php',
        '\Fi1a\UserSettings\Option' => 'lib/Option.php',

        // События
        '\Fi1a\UserSettings\Events\SprintMigration' => 'lib/Events/SprintMigration.php',

        //Классы для модуля sprint.migration
        '\Fi1a\UserSettings\SprintMigration\Builders\UserSettingsBuilder' => 'lib/SprintMigration/Builders/UserSettingsBuilder.php',
        '\Fi1a\UserSettings\SprintMigration\Helpers\UserSettingsHelper' => 'lib/SprintMigration/Helpers/UserSettingsHelper.php',
    ]
);

global $APPLICATION;

ModuleRegistry::configure($APPLICATION, $GLOBALS);