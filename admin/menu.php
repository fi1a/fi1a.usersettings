<?php

namespace Fi1a\UserSettings;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

global $APPLICATION;

$moduleId = 'fi1a.usersettings';

$rightForModule = $APPLICATION->GetGroupRight($moduleId);

// Если нет прав - не продолжаем
if ('D' == $rightForModule) {
    return false;
}

Loc::loadMessages(__FILE__);

$menu = [
    'PARENT_MENU' => \Bitrix\Main\Config\Option::get('fi1a.usersettings', 'PARENT_MENU', 'global_menu_services'),
    'SORT' => \Bitrix\Main\Config\Option::get('fi1a.usersettings', 'SORT', 2000),
    'LOCALIZATION' => unserialize(\Bitrix\Main\Config\Option::get('fi1a.usersettings', 'LOCALIZATION')),
];

$menuItem = [
    [
        'parent_menu' => $menu['PARENT_MENU'] ? $menu['PARENT_MENU'] : 'global_menu_services',
        'sort' => $menu['SORT'] ? $menu['SORT'] : 2000,
        'text' => $menu['LOCALIZATION'][LANGUAGE_ID]['MENU_TEXT'] ? $menu['LOCALIZATION'][LANGUAGE_ID]['MENU_TEXT'] : Loc::getMessage('FUS_MENU_TEXT'),
        'url'  => 'fi1a_usersettings.php?lang='.LANGUAGE_ID,
        'title'=> $menu['LOCALIZATION'][LANGUAGE_ID]['MENU_TITLE'] ? $menu['LOCALIZATION'][LANGUAGE_ID]['MENU_TITLE'] : Loc::getMessage('FUS_MENU_TITLE'),
        'icon' => 'fi1a_usersettings_menu_icon',
    ]
];

return $menuItem;

