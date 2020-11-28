<?php
namespace Fi1a\UserSettings;

use \Bitrix\Main\Loader;

Loader::registerAutoloadClasses(
    'fi1a.usersettings',
    [
        // Хелперы
        '\Fi1a\UserSettings\Helpers\Flush' => 'lib/Helpers/Flush.php',

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
        '\Fi1a\UserSettings\ITab' => 'lib/ITab.php',
        '\Fi1a\UserSettings\Tab' => 'lib/Tab.php',
        '\Fi1a\UserSettings\IField' => 'lib/IField.php',
        '\Fi1a\UserSettings\Field' => 'lib/Field.php',
        '\Fi1a\UserSettings\IBaseCollection' => 'lib/IBaseCollection.php',
        '\Fi1a\UserSettings\ABaseCollection' => 'lib/ABaseCollection.php',
        '\Fi1a\UserSettings\ITabCollection' => 'lib/ITabCollection.php',
        '\Fi1a\UserSettings\TabCollection' => 'lib/TabCollection.php',
        '\Fi1a\UserSettings\ITabMapper' => 'lib/ITabMapper.php',
        '\Fi1a\UserSettings\TabMapper' => 'lib/TabMapper.php',
        '\Fi1a\UserSettings\IFieldCollection' => 'lib/IFieldCollection.php',
        '\Fi1a\UserSettings\FieldCollection' => 'lib/FieldCollection.php',
        '\Fi1a\UserSettings\IFieldMapper' => 'lib/IFieldMapper.php',
        '\Fi1a\UserSettings\FieldMapper' => 'lib/FieldMapper.php',
        '\Fi1a\UserSettings\IOption' => 'lib/IOption.php',
        '\Fi1a\UserSettings\Option' => 'lib/Option.php',
    ]
);
