<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
use Fi1a\UserSettings\Helpers\Flush;
use Fi1a\UserSettings\UserTypeManager;

/**
 * @var array $arResult
 * @var array $arParams
 * @global $USER_FIELD_MANAGER
 */

if (!empty($arResult['ERRORS'])) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_ERROR'),
        'DETAILS' => implode('<br/>', $arResult['ERRORS']),
        'HTML' => 'Y',
        'TYPE' => 'ERROR',
    ]);
}
if ('SUCCESS' == $arResult['STATUS']) {
    \CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage('FUS_SAVE_SUCCESS'),
        'TYPE' => 'OK',
    ]);
}
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

if (!count($arResult['TABS'])) {
    if (empty($arResult['ERRORS'])) {
        require __DIR__ . '/empty.php';
        require __DIR__ . '/note.php';
    }

    return;
}

$tabs = [];
foreach ($arResult['TABS'] as $tabId => $tab) {
    $tab['FIELDS'] = [];
    foreach ($arResult['FIELDS'] as $field) {
        if ($field['TAB_ID'] != $tab['ID']) {
            continue;
        }

        $tab['FIELDS'][] = $field;
    }
    unset($field);

    uasort($tab['FIELDS'], function ($a, $b) {
        if ($a['UF']['SORT'] == $b['UF']['SORT']) {
            return $a['ID'] == $b['ID'];
        }

        return $a['UF']['SORT'] - $b['UF']['SORT'];
    });

    $tabs[] = [
        'DIV' => $tab['ID'],
        'TAB' => $tab->getName($arParams['LANGUAGE_ID']),
        'TITLE' => $tab->getTitle($arParams['LANGUAGE_ID']),
        'FIELDS' => [
            [
                'id' => 1,
                'CONTENT' => '123',
            ],
        ],
    ];
    $arResult['TABS'][$tabId] = $tab;
}
unset($tab, $tabId);

if (empty($tabs)) {
    require __DIR__ . '/empty.php';
    require __DIR__ . '/note.php';

    return;
}

global $USER_FIELD_MANAGER;

if($USER_FIELD_MANAGER && method_exists($USER_FIELD_MANAGER, 'showscript')) {
    echo $USER_FIELD_MANAGER->ShowScript();
}

$tabControl = new \CAdminTabControl('tabControl', $tabs);
?>
<form action="" method="post" enctype="multipart/form-data">
    <?php
    $tabControl->Begin();

    foreach ($tabs as $tab) {
        $tabControl->BeginNextTab();

        foreach ($arResult['TABS'][$tab['DIV']]['FIELDS'] as $field) {
            $userField = $field['UF'];

            $userField['VALUE_ID'] = $arParams['VALUE_ID'];
            $label = $userField['EDIT_FORM_LABEL'][$arParams['LANGUAGE_ID']] ? $userField['EDIT_FORM_LABEL'][$arParams['LANGUAGE_ID']] : $userField['FIELD_NAME'];
            $userField['EDIT_FORM_LABEL'] = $label;

            ?>
            <tr<?php if ($userField['MANDATORY'] == 'Y') { ?> class="adm-detail-required-field"<?php } ?>>
                <td valign="top" width="40%" class="adm-detail-content-cell-l">
                    <?= \htmlspecialcharsbx($label)?>:
                </td>
                <td valign="top" width="60%" class="adm-detail-content-cell-r">
                    <?= UserTypeManager::getInstance()->GetEditFormHTML($arResult['VARS_FROM_FORM'], $arResult['FORM'][$userField['FIELD_NAME']], $userField);?>
                    <?php
                    if ($userField['HELP_MESSAGE'][$arParams['LANGUAGE_ID']]) {
                        echo BeginNote();
                        echo \htmlspecialcharsbx($userField['HELP_MESSAGE'][$arParams['LANGUAGE_ID']]);
                        echo EndNote();
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        unset($field);
    }
    unset($tab);

    echo bitrix_sessid_post();

    $tabControl->Buttons(
        array(
            "disabled" => $arParams['RIGHT'] < 'F',
        )
    );

    $tabControl->End();
?>
</form>
<?php
require __DIR__ . '/note.php';