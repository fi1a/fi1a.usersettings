<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\SiteTable;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Uri;
use Fi1a\UserSettings\Helpers\Flush;

global $APPLICATION;

$module_id = 'fi1a.usersettings';

$moduleMode = Loader::includeSharewareModule($module_id);
$rightForModule = $APPLICATION->GetGroupRight($module_id);

// Если нет прав, или не установлен модуль - не продолжаем
if ('R' > $rightForModule || !in_array($moduleMode, [Loader::MODULE_DEMO, Loader::MODULE_INSTALLED])) {
    return;
}

Extension::load('jquery');
Extension::load('ui.vue');

Asset::getInstance()->addJs('/bitrix/js/fi1a.usersettings/options.js');

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

$isSubmit = $request->isPost()
    && ($request->getPost('save') || $request->getPost('apply'))
    && check_bitrix_sessid();

// Если нет прав, а форму отправили
if ($isSubmit && $rightForModule < 'W') {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_NO_RIGHTS'),
        'TYPE' => 'ERROR',
    ]);

    $isSubmit = false;
}

$backUrlSettings = $request->getQuery('back_url_settings');
if (!$backUrlSettings) {
    $backUrlSettings = $request->getPost('back_url_settings');
}

$errors = [];
$fusTabs = null;
$fusFields = null;

// Удаление вкладки
if ($request->getQuery('tabDelete')) {
    $id = (int)$request->getQuery('tabDelete');

    $tab = TabMapper::getById($id);
    if ($tab) {
        $result = $tab->delete();

        if ($result->isSuccess()) {
            // Редиректим
            Flush::set('FUS_TAB_DELETE_SUCCESS', true);

            $uri = new Uri($request->getRequestUri());
            $uri->deleteParams(['tabDelete']);

            \LocalRedirect($uri->getUri());
        }

        $errors = $result->getErrorMessages();
    } else {
        $errors[] = Loc::getMessage('FUS_TAB_NOT_FOUND', ['#ID#' => $id,]);
    }
}

// Удаление поля
if ($request->getQuery('fieldDelete')) {
    $id = (int)$request->getQuery('fieldDelete');

    $field = FieldMapper::getById($id);
    if ($field) {
        $result = $field->delete();

        if ($result->isSuccess()) {
            // Редиректим
            Flush::set('FUS_FIELD_DELETE_SUCCESS', true);

            $uri = new Uri($request->getRequestUri());
            $uri->deleteParams(['fieldDelete']);

            \LocalRedirect($uri->getUri());
        }

        $errors = $result->getErrorMessages();
    } else {
        $errors[] = Loc::getMessage('FUS_FIELD_NOT_FOUND', ['#ID#' => $id,]);
    }
}

$allowParentMenu = [
    'global_menu_content' => Loc::getMessage('FUS_MENU_CONTENT'),
    'global_menu_services' => Loc::getMessage('FUS_MENU_SERVICES'),
    'global_menu_store' => Loc::getMessage('FUS_MENU_STORES'),
    'global_menu_statistics' => Loc::getMessage('FUS_MENU_STATISTICS'),
    'global_menu_settings' => Loc::getMessage('FUS_MENU_SETTINGS'),
];

$menu = [
    'PARENT_MENU' => \Bitrix\Main\Config\Option::get('fi1a.usersettings', 'PARENT_MENU', 'global_menu_settings'),
    'SORT' => \Bitrix\Main\Config\Option::get('fi1a.usersettings', 'SORT', 1780),
    'LOCALIZATION' => unserialize(\Bitrix\Main\Config\Option::get('fi1a.usersettings', 'LOCALIZATION')),
];

if ($isSubmit) {
    // Сохранение табов
    $fusTabs = (array)$request->getPost('TABS');
    foreach ($fusTabs as $tab) {
        $tab['SITES'] = (array)$tab['SITES'];

        $tab['CODE'] = \htmlspecialcharsbx(trim($tab['CODE']));
        $tab['ACTIVE'] = 1 == $tab['ACTIVE'] ? 1 : 0;
        $tab['SORT'] = (int)$tab['SORT'];
        if (!$tab['SORT']) {
            $tab['SORT'] = 500;
        }

        try {
            $instanceTab = Tab::create($tab);
            $result = $instanceTab->update();

            if ($result->isSuccess()) {
                continue;
            }

            $errors = array_merge($errors, $result->getErrorMessages());
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }
    }
    unset($tab);

    // Сохранение полей
    $fusFields = (array)$request->getPost('FIELDS');
    foreach ($fusFields as $field) {
        $field['SITES'] = (array)$field['SITES'];
        $field['ACTIVE'] = 1 == $field['ACTIVE'] ? 1 : 0;
        $field['UF']['SORT'] = (int)$field['UF']['SORT'];
        if (!$field['UF']['SORT']) {
            $field['UF']['SORT'] = 500;
        }

        try {
            $fieldInstance = Field::create($field);
            $result = $fieldInstance->update();

            if ($result->isSuccess()) {
                continue;
            }

            $errors = array_merge($errors, $result->getErrorMessages());
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }
    }
    unset($field);

    // Сохранение настроек меню
    $menu = (array)$request->getPost('MENU');
    if (!array_key_exists($menu['PARENT_MENU'], $allowParentMenu)) {
        $errors[] = Loc::getMessage('FUS_PARENT_MENU_ERROR');
    }
    $menu['SORT'] = (int)$menu['SORT'];

    if (empty($errors)) {
        \Bitrix\Main\Config\Option::set('fi1a.usersettings', 'PARENT_MENU', $menu['PARENT_MENU']);
        \Bitrix\Main\Config\Option::set('fi1a.usersettings', 'SORT', $menu['SORT']);
        \Bitrix\Main\Config\Option::set('fi1a.usersettings', 'LOCALIZATION', serialize($menu['LOCALIZATION']));
    }

    if (empty($errors)) {
        // Редиректим
        Flush::set('FUS_EDIT_SUCCESS', true);

        $uri = new Uri($request->getRequestUri());
        if ($request->getPost('save') && $backUrlSettings) {
            $uri = new Uri($backUrlSettings);
        }

        \LocalRedirect($uri->getUri());
    }
}

$languages = LanguageTable::getList([
    'order' => [
        'SORT' => 'ASC',
    ]
])->fetchAll();

$sites = SiteTable::getList([
    'order' => [
        'SORT' => 'ASC',
    ],
])->fetchAll();

if (is_null($fusTabs)) {
    $fusTabs = TabMapper::getList()->toArray();
}

if (is_null($fusFields)) {
    $fusFields = [];
}
$fusFields = array_merge_recursive(FieldMapper::getList()->toArray(), $fusFields);

if (Flush::get('FUS_TAB_ADD_SUCCESS')) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_TAB_ADD_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}
if (Flush::get('FUS_EDIT_SUCCESS')) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_EDIT_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}
if (Flush::get('FUS_TAB_DELETE_SUCCESS')) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_TAB_DELETE_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}
if (Flush::get('FUS_FIELD_DELETE_SUCCESS')) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_FIELD_DELETE_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}
if (Flush::get('FUS_ADD_FIELD_SUCCESS')) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_ADD_FIELD_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}

if (!empty($errors)) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_ERROR_TITLE'),
        'DETAILS' => implode('<br/>', $errors),
        'HTML' => 'Y',
        'TYPE' => 'ERROR',
    ]);
}

$tabs = [
    [
        'DIV' => 'settings',
        'TAB' => Loc::getMessage('FUS_SETTINGS_TAB'),
        'TITLE' => Loc::getMessage('FUS_SETTINGS_TITLE'),
    ],
    [
        'DIV' => 'menu',
        'TAB' => Loc::getMessage('FUS_MENU_TAB'),
        'TITLE' => Loc::getMessage('FUS_MENU_TITLE'),
    ],
    [
        'DIV' => 'rights',
        'TAB' => Loc::getMessage('MAIN_TAB_RIGHTS'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_RIGHTS'),
    ],
];

$tabControl = new \CAdminTabControl('tabControl', $tabs);

?>
<form method="POST" action="">
    <?php
    $tabControl->Begin();

    foreach ($tabs as $tab) {
        $tabControl->BeginNextTab();

        switch ($tab['DIV']) {
            case 'settings':
                // Редактирование пользовательских настроек

                require_once __DIR__ . '/options_settings.php';

                break;
            case 'menu':
                // Меню и заголовки

                require_once __DIR__ . '/options_menu.php';

                break;
            case 'rights':
                // Права на модуль

                require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';

                break;
        }
    }
    unset($tab);

    $tabControl->Buttons(
        array(
            "disabled" => ($rightForModule < "W"),
            "back_url" => !empty($backUrlSettings) ? $backUrlSettings : null
        )
    );

    echo bitrix_sessid_post();

    $tabControl->End();
    ?>
</form>
<?php
echo BeginNote();
?>
<p><?= Loc::getMessage('FUS_NOTE_1', ['#LANGUAGE_ID#' => LANGUAGE_ID])?></p>
<p><?= Loc::getMessage('FUS_NOTE_2')?></p>
<p><?= Loc::getMessage('FUS_NOTE_3')?></p>
<p><?= Loc::getMessage('FUS_NOTE_4')?></p>
<?php
echo EndNote();
