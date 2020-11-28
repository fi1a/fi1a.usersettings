<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use Fi1a\UserSettings\Helpers\Flush;
use Bitrix\Main\Web\PostDecodeFilter;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;

$moduleId = 'fi1a.usersettings';

Loc::loadMessages(__FILE__);

$moduleMode = Loader::includeSharewareModule($moduleId);
$rightForModule = $APPLICATION->GetGroupRight($moduleId);

$popupWindow = new \CJSPopup(Loc::getMessage('FUS_TAB_POPUP_TITLE'));

// Если нет прав, или не установлен модуль - не продолжаем
if ('W' > $rightForModule || !in_array($moduleMode, [Loader::MODULE_DEMO, Loader::MODULE_INSTALLED])) {
    $popupWindow->ShowError(Loc::getMessage('FUS_NO_RIGHTS'));

    return;
}

$request = Application::getInstance()->getContext()->getRequest();
$request->addFilter(new PostDecodeFilter());

// Если не ajax, выходим
if ('core_window_cdialog' != $request->getQuery('bxsender')
    || (!$request->isAjaxRequest() && 'true' != $request->getHeader('Bx-ajax'))
) {
    $popupWindow->ShowError(Loc::getMessage('FUS_ONLY_AJAX_ACCESS'));

    return;
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

$errors = [];

$fields = [
    'CODE' => '',
    'ACTIVE' => 1,
    'SORT' => 500,
    'LOCALIZATION' => [],
];

if ($request->isPost()) {
    if (!check_bitrix_sessid()) {
        $errors[] = Loc::getMessage('FUS_SESSID_ERROR');
    }

    if (empty($errors)) {
        $fields['CODE'] = \htmlspecialcharsbx(trim($request->getPost('CODE')));
        if (!$fields['CODE']) {
            $errors[] = Loc::getMessage('FUS_CODE_EMPTY');
        } elseif (!preg_match('/^[0-9A-Za-z_]+$/', $fields['CODE'])) {
            $errors[] = Loc::getMessage('FUS_CODE_VALIDATION_ERROR');
        }

        $fields['ACTIVE'] = 'Y' == $request->getPost('ACTIVE') ? 1 : 0;

        $fields['SORT'] = (int)$request->getPost('SORT');
        if (!$fields['SORT']) {
            $fields['SORT'] = 500;
        }

        $lNames = $request->getPost('L_NAME');
        $lTitles = $request->getPost('L_TITLE');
        foreach ($languages as $language) {
            $fields['LOCALIZATION'][$language['LID']] = [
                'L_NAME' => trim($lNames[$language['LID']]),
                'L_TITLE' => trim($lTitles[$language['LID']]),
            ];
        }
        unset($language);

        if (empty($errors)) {
            // Добавляем новую вкладку
            try {
                $tab = Tab::create($fields);
                $result = $tab->add();

                if ($result->isSuccess()) {
                    Flush::set('FUS_TAB_ADD_SUCCESS', true);

                    $popupWindow->Close(true);

                    die();
                }

                $errors = $result->getErrorMessages();
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }
    }
}

$popupWindow->ShowTitlebar(Loc::getMessage('FUS_TAB_POPUP_TITLE'));

if (!empty($errors)) {
    foreach ($errors as $error) {
        $popupWindow->ShowValidationError($error);
    }
}

$popupWindow->StartDescription("bx-property-page");
?>
<p><?= Loc::getMessage('FUS_TAB_EDIT_DESCRIPTION')?></p>
<?php
$popupWindow->EndDescription();

$popupWindow->StartContent();
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
        <tr class="heading">
            <td colspan="2"><?= Loc::getMessage('FUS_MAIN_SETTINGS')?></td>
        </tr>
        <tr class="adm-detail-required-field">
            <td width="40%" class="adm-detail-content-cell-l"><label for="field-code"><?= Loc::getMessage('FUS_FIELD_CODE')?>:</label></td>
            <td width="60%" class="adm-detail-content-cell-r"><input id="field-code" class="b-fus-field" type="text" name="CODE" value="<?= \htmlspecialcharsbx($fields['CODE'])?>" /></td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><label for="field-active"><?= Loc::getMessage('FUS_FIELD_ACTIVE')?>:</label></td>
            <td width="60%" class="adm-detail-content-cell-r">
            <input<?php if ($fields['ACTIVE']) { ?> checked<?php } ?> type="checkbox" name="ACTIVE" id="field-active" value="1" class="adm-designed-checkbox">
            <label class="adm-designed-checkbox-label" for="field-active" title=""></label>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><label for="field-sort"><?= Loc::getMessage('FUS_FIELD_SORT')?>:</label></td>
            <td width="60%" class="adm-detail-content-cell-r"><input id="field-sort" class="b-fus-field" type="text" name="SORT" value="<?= $fields['SORT']?>" /></td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?= Loc::getMessage('FUS_LOCALIZATION_SETTINGS')?></td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="b-fus-table">
                    <tbody>
                        <tr>
                            <td><?= Loc::getMessage('FUS_FIELD_LANG')?></td>
                            <td><?= Loc::getMessage('FUS_FIELD_NAME')?></td>
                            <td><?= Loc::getMessage('FUS_FIELD_TITLE')?></td>
                        </tr>
                        <?php foreach ($languages as $language) { ?>
                            <tr>
                                <td><?= $language['LID']?></td>
                                <td><input class="b-fus-field" type="text" name="L_NAME[<?= $language['LID']?>]" value="<?= \htmlspecialcharsbx($fields['LOCALIZATION'][$language['LID']]['L_NAME'])?>" /></td>
                                <td><input class="b-fus-field" type="text" name="L_TITLE[<?= $language['LID']?>]" value="<?= \htmlspecialcharsbx($fields['LOCALIZATION'][$language['LID']]['L_TITLE'])?>" /></td>
                            </tr>
                        <?php } ?>
                        <?php
                        unset($language);
                        ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<?php
$popupWindow->EndContent();

$popupWindow->ShowStandardButtons();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
