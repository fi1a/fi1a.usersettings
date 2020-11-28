<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;

Loc::loadMessages(__FILE__);

$localization = unserialize(\Bitrix\Main\Config\Option::get('fi1a.usersettings', 'LOCALIZATION'));

$APPLICATION->SetTitle($localization[LANGUAGE_ID]['PAGE_TITLE'] ? $localization[LANGUAGE_ID]['PAGE_TITLE'] : Loc::getMessage('FUS_SET_OPTIONS_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->IncludeComponent(
    'fi1a:usersettings.admin',
    '',
    [
    ]
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
