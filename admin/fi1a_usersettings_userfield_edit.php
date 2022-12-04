<?php

namespace Fi1a\UserSettings;

/**
 * @var $adminPage
 * @var $adminSidePanelHelper
 * @var $USER_FIELD_MANAGER
 */

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Type\Collection;
use Bitrix\Main\UI\Extension;
use Fi1a\UserSettings\Helpers\Flush;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $APPLICATION;

$moduleId = 'fi1a.usersettings';

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/userfield_edit.php');

$moduleMode = Loader::includeSharewareModule($moduleId);
$rightForModule = $APPLICATION->GetGroupRight($moduleId);

$popupWindow = new \CJSPopup(Loc::getMessage('FUS_FIELD_POPUP_TITLE'));

// Если нет прав, или не установлен модуль - не продолжаем
if ('W' > $rightForModule || !in_array($moduleMode, [Loader::MODULE_DEMO, Loader::MODULE_INSTALLED])) {
    $popupWindow->ShowError(Loc::getMessage('FUS_NO_RIGHTS'));

    return;
}

Extension::load('jquery');

$request = Application::getInstance()->getContext()->getRequest();

// Если не ajax, выходим
if ('core_window_cdialog' != $request->getQuery('bxsender')
    || (!$request->isAjaxRequest() && 'true' != $request->getHeader('Bx-ajax'))
) {
    $popupWindow->ShowError(Loc::getMessage('FUS_ONLY_AJAX_ACCESS'));

    return;
}

$tabs = TabMapper::getList();


define("HELP_FILE", "settings/userfield_edit.php");

$ID = (int)$request->getQuery('ID');
$back_url = $_REQUEST["back_url"];
$list_url = $_REQUEST["list_url"];

$selfFolderUrl = $adminPage->getSelfFolderUrl();
if ($adminSidePanelHelper->isPublicFrame())
{
    $back_url = $adminSidePanelHelper->setDefaultQueryParams($back_url);
}

$allSites = SiteTable::getList([
    'order' => [
        'SORT' => 'ASC',
    ],
])->fetchAll();

/** @var \CUserFieldEnum $obEnum */
$obEnum = null;
if($ID>0)
{
    $arUserField = FieldMapper::getById($ID);
    if ($arUserField) {
        $arUserField = $arUserField->getArrayCopy();
        $arUserField = array_merge($arUserField, $arUserField['UF']);
        $UF_ID = $arUserField['UF_ID'];

        if($arType = $USER_FIELD_MANAGER->GetUserType($arUserField["USER_TYPE_ID"]))
        {
            if($arType["BASE_TYPE"] == "enum")
            {
                $obEnum = new \CUserFieldEnum;
            }
        }
    }
}

$bVarsFromForm = false;
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST['FIELD_EDIT']['SAVE'] && check_bitrix_sessid())
{
    $isUpdate = false;

    $adminSidePanelHelper->decodeUriComponent();

    $arFields = array(
        'ID' => $ID,
        'TAB_ID' => (int)$_REQUEST['TAB_ID'],
        'ACTIVE' => $_REQUEST['ACTIVE'] == 'Y' ? 1 : 0,
        'UF_ID' => (int)$_REQUEST['UF_ID'],
        'UF' => [
            'ENTITY_ID' => $_REQUEST['ENTITY_ID'],
            'FIELD_NAME' => $_REQUEST['FIELD_NAME'],
            'USER_TYPE_ID' => $_REQUEST['USER_TYPE_ID'],
            'XML_ID' => $_REQUEST['XML_ID'],
            'SORT' => $_REQUEST['SORT'],
            'MULTIPLE' => $_REQUEST['MULTIPLE'],
            'MANDATORY' => $_REQUEST['MANDATORY'],
            'SETTINGS' => $_REQUEST['SETTINGS'],
            'EDIT_FORM_LABEL' => $_REQUEST['EDIT_FORM_LABEL'],
            'LIST_COLUMN_LABEL' => $_REQUEST['LIST_COLUMN_LABEL'],
            'LIST_FILTER_LABEL' => $_REQUEST['LIST_FILTER_LABEL'],
            'ERROR_MESSAGE' => $_REQUEST['ERROR_MESSAGE'],
            'HELP_MESSAGE' => $_REQUEST['HELP_MESSAGE'],
        ]
    );

    $field = Field::create($arFields);
    if($UF_ID > 0) {
        $result = $field->update();

        if ($result->isSuccess()) {
            $res = true;
        } else {
            $errors = $result->getErrorMessages();
        }
    } else {
        $result = $field->add();

        if ($result->isSuccess()) {
            $UF_ID = $result->getData()['UF_ID'];
            $res = true;
        } else {
            $errors = $result->getErrorMessages();
        }
    }

    if(is_object($obEnum))
    {
        $LIST = $_REQUEST["LIST"];
        if(is_array($LIST))
        {
            foreach($LIST as $id => $value)
                if(is_array($value))
                    $LIST[$id]["DEF"] = "N";
        }
        if(is_array($LIST["DEF"]))
        {
            foreach($LIST["DEF"] as $value)
                if(is_array($LIST[$value]))
                    $LIST[$value]["DEF"] = "Y";
            unset($LIST["DEF"]);
        }
        $res = $obEnum->SetEnumValues($UF_ID, $LIST);
    }

    if($res)
    {
        if ($isUpdate) {
            Flush::set('FUS_EDIT_FIELD_SUCCESS', true);

            $popupWindow->Close(true);

            die();
        }

        Flush::set('FUS_ADD_FIELD_SUCCESS', true);

        $popupWindow->Close(true);
    }
    else
    {
        $bVarsFromForm = true;
    }

}

if($ID>0)
{
    $arUserField = FieldMapper::getById($ID);
    if ($arUserField) {
        $arUserField = $arUserField->getArrayCopy();
        $arUserField = array_merge($arUserField, $arUserField['UF']);
        $UF_ID = $arUserField['UF_ID'];

        if($arType = $USER_FIELD_MANAGER->GetUserType($arUserField["USER_TYPE_ID"]))
        {
            if($arType["BASE_TYPE"] == "enum")
            {
                $obEnum = new \CUserFieldEnum;
            }
        }
    } else {
        $UF_ID=0;
        $ID=0;
    }
}
else
{
    $arUserField = array(
        'TAB_ID' => isset($_REQUEST['TAB_ID'])? (int)$_REQUEST['TAB_ID']: '',
        'ACTIVE' => isset($_REQUEST['ACTIVE']) && 'Y' != $_REQUEST['ACTIVE'] ? 0 : 1,
        'ENTITY_ID' => isset($_REQUEST['ENTITY_ID'])? $_REQUEST['ENTITY_ID']: '',
        'FIELD_NAME' => isset($_REQUEST['FIELD_NAME'])? $_REQUEST['FIELD_NAME']: 'UF_',
        'USER_TYPE_ID' => isset($_REQUEST['USER_TYPE_ID'])? $_REQUEST['USER_TYPE_ID']: '',
        'XML_ID' => isset($_REQUEST['XML_ID'])? $_REQUEST['XML_ID']: '',
        'SORT' => isset($_REQUEST['SORT']) ? $_REQUEST['SORT'] : 500,
        'MULTIPLE' => isset($_REQUEST['MULTIPLE']) ? $_REQUEST['MULTIPLE'] : 'N',
        'MANDATORY' => isset($_REQUEST['MANDATORY']) ? $_REQUEST['MANDATORY'] : 'N',
        'SETTINGS' => array(),
    );
}

if($bVarsFromForm)
{
    $TAB_ID = (int)$_REQUEST['TAB_ID'];
    $ACTIVE = $_REQUEST['ACTIVE'] == 'Y' ? 1 : 0;
    
    $FIELD_NAME = htmlspecialcharsbx($_REQUEST['FIELD_NAME']);
    $USER_TYPE_ID = htmlspecialcharsbx($_REQUEST['USER_TYPE_ID']);
    $XML_ID = htmlspecialcharsbx($_REQUEST['XML_ID']);
    $SORT = htmlspecialcharsbx($_REQUEST['SORT']);
    $MULTIPLE = htmlspecialcharsbx($_REQUEST['MULTIPLE']);
    $MANDATORY = htmlspecialcharsbx($_REQUEST['MANDATORY']);
}
else
{
    $TAB_ID = (int)$arUserField['TAB_ID'];
    $ACTIVE = $arUserField['ACTIVE'];

    $FIELD_NAME = htmlspecialcharsbx($arUserField['FIELD_NAME']);
    $USER_TYPE_ID = htmlspecialcharsbx($arUserField['USER_TYPE_ID']);
    $XML_ID = htmlspecialcharsbx($arUserField['XML_ID']);
    $SORT = htmlspecialcharsbx($arUserField['SORT']);
    $MULTIPLE = htmlspecialcharsbx($arUserField['MULTIPLE']);
    $MANDATORY = htmlspecialcharsbx($arUserField['MANDATORY']);
}

$popupWindow->ShowTitlebar(Loc::getMessage('FUS_FIELD_POPUP_TITLE'));

$popupWindow->StartContent();
?>


<?
if (!empty($errors)) {
    foreach ($errors as $error) {
        $popupWindow->ShowValidationError($error);
    }
}

?>
<script language="JavaScript">
    <!--
    function addNewRow(tableID)
    {
        var tbl = document.getElementById(tableID);
        var cnt = tbl.rows.length;
        var oRow = tbl.insertRow(cnt);
        for(var i=0;i<6;i++)
        {
            var oCell = oRow.insertCell(i);
            var sHTML=tbl.rows[cnt-1].cells[i].innerHTML;
            var p = 0;
            while(true)
            {
                var s = sHTML.indexOf('[n',p);
                if(s<0)break;
                var e = sHTML.indexOf(']',s);
                if(e<0)break;
                var n = parseInt(sHTML.substr(s+2,e-s));
                sHTML = sHTML.substr(0, s)+'[n'+(++n)+']'+sHTML.substr(e+1);
                p=s+1;
            }
            while(true)
            {
                s = sHTML.indexOf('\"n',p);
                if(s<0)break;
                e = sHTML.indexOf('\"',s+1);
                if(e<0)break;
                n = parseInt(sHTML.substr(s+2,e-s));
                sHTML = sHTML.substr(0, s)+'\"n'+(++n)+'\"'+sHTML.substr(e+1);
                p=s+1;
            }
            oCell.innerHTML = sHTML;
        }

        setTimeout(function() {
            var r = BX.findChildren(oCell.parentNode, {tag: /^(input|select|textarea)$/i}, true);
            if (r && r.length > 0)
            {
                for (var i=0,l=r.length;i<l;i++)
                {
                    if (r[i].form && r[i].form.BXAUTOSAVE)
                        r[i].form.BXAUTOSAVE.RegisterInput(r[i]);
                    else
                        break;
                }
            }
        }, 10);
    }

    BX.ready(function(){
        BX.addCustomEvent(document.forms.post_form, 'onAutoSaveRestore', function(ob, data)
        {
            for(var i in data)
            {
                var r = /^LIST\[n([\d]+)\]\[XML_ID\]$/.exec(i);
                if (r && r[1] > 0)
                {
                    addNewRow('list_table');
                }
            }

        });

    });
    //-->
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
        <?if($ID):?>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">ID:</td>
                <td width="60%" class="adm-detail-content-cell-r"><?=$ID?></td>
            </tr>
        <?endif?>
        <tr class="adm-detail-required-field">
            <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('FUS_FIELD_TAB')?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select class="b-fus-select" name="TAB_ID">
                    <?php foreach($tabs as $tab) { ?>
                        <option<?php if ($TAB_ID == $tab['ID']) { ?> selected<?php } ?> value="<?= $tab['ID']?>"><?= $tab->getName()?></option>
                    <?php } ?>
                    <?php
                    unset($tab);
                    ?>
                </select>
                <input type="hidden" name="ENTITY_ID" value="<?= OptionInterface::ENTITY_ID?>">
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_USER_TYPE_ID")?>:</td>
            <td class="adm-detail-content-cell-r">
                <?
                if($UF_ID > 0)
                {
                    $arUserType = $USER_FIELD_MANAGER->GetUserType($USER_TYPE_ID);
                    echo htmlspecialcharsbx($arUserType["DESCRIPTION"]);
                }
                else
                {
                    $arUserTypes = $USER_FIELD_MANAGER->GetUserType();
                    $arr = array("reference"=>array(), "reference_id"=>array());
                    Collection::sortByColumn($arUserTypes, 'DESCRIPTION', '', null, true);
                    foreach($arUserTypes as $arUserType)
                    {
                        $arr["reference"][] = $arUserType["DESCRIPTION"];
                        $arr["reference_id"][] = $arUserType["USER_TYPE_ID"];
                    }
                    echo SelectBoxFromArray("USER_TYPE_ID", $arr, $USER_TYPE_ID, "", 'OnChange="'.htmlspecialcharsbx('$(\'[name="FIELD_EDIT[SAVE]"]\').val(\'\'); window.fusAddFieldDialog.PostParameters();').'"');
                }
                ?>
            </td>
        </tr>
        <tr class="adm-detail-required-field">
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_FIELD_NAME")?>:</td>
            <td class="adm-detail-content-cell-r">
                <?if($UF_ID>0):?>
                    <?=$FIELD_NAME?>
                <?else:?>
                    <input type="text" name="FIELD_NAME" value="<?=$FIELD_NAME?>" maxlength="20">
                <?endif?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><?= Loc::getMessage('FUS_FIELD_ACTIVE')?>:</td>
            <td class="adm-detail-content-cell-r"><input type="checkbox" name="ACTIVE" value="1"<?if($ACTIVE == 1) echo " checked"?> ></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_XML_ID")?>:</td>
            <td class="adm-detail-content-cell-r"><input type="text" name="XML_ID" value="<?=$XML_ID?>" maxlength="255"></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_SORT")?>:</td>
            <td class="adm-detail-content-cell-r"><input type="text" name="SORT" value="<?= $SORT ? $SORT : 500?>"></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_MULTIPLE")?>:</td>
            <td class="adm-detail-content-cell-r">
                <?if($UF_ID>0):?>
                    <?=$MULTIPLE == "Y"? GetMessage("MAIN_YES"): GetMessage("MAIN_NO")?>
                <?else:?>
                    <input type="checkbox" name="MULTIPLE" value="Y"<?if($MULTIPLE == "Y") echo " checked"?> >
                <?endif?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><?=GetMessage("USERTYPE_MANDATORY")?>:</td>
            <td class="adm-detail-content-cell-r"><input type="checkbox" name="MANDATORY" value="Y"<?if($MANDATORY == "Y") echo " checked"?> ></td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?echo GetMessage("USERTYPE_SETTINGS")?></td>
        </tr>
        <?if($UF_ID > 0):
            echo $USER_FIELD_MANAGER->GetSettingsHTML($arUserField, $bVarsFromForm);
        else:
            $arUserType = $USER_FIELD_MANAGER->GetUserType($USER_TYPE_ID);
            if(!$arUserType)
                $arUserType = array_shift($arUserTypes);
            echo $USER_FIELD_MANAGER->GetSettingsHTML($arUserType["USER_TYPE_ID"], $bVarsFromForm);
        endif;?>
        <tr class="heading">
            <td colspan="2"><?echo GetMessage("USERTYPE_LANG_SETTINGS")?></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <table border="0" cellspacing="10" cellpadding="2">
                    <tr>
                        <td align="right"><?echo GetMessage("USER_TYPE_LANG");?></td>
                        <td align="center" width="200"><?echo GetMessage("USER_TYPE_EDIT_FORM_LABEL");?></td>
                        <td align="center" width="200"><?echo GetMessage("USER_TYPE_HELP_MESSAGE");?></td>
                    </tr>
                    <?
                    $rsLanguage = \CLanguage::GetList($by, $order, array());
                    while($arLanguage = $rsLanguage->Fetch()):
                        $htmlLID = htmlspecialcharsbx($arLanguage["LID"]);
                        ?>
                        <tr>
                            <td align="right"><?echo htmlspecialcharsbx($arLanguage["NAME"])?>:</td>
                            <td align="center"><input type="text" name="EDIT_FORM_LABEL[<?echo $htmlLID?>]" size="40" maxlength="255" value="<?echo htmlspecialcharsbx($bVarsFromForm? $_REQUEST["EDIT_FORM_LABEL"][$arLanguage["LID"]]: $arUserField["EDIT_FORM_LABEL"][$arLanguage["LID"]])?>"></td>
                            <td align="center"><input type="text" name="HELP_MESSAGE[<?echo $htmlLID?>]" size="40" maxlength="255" value="<?echo htmlspecialcharsbx($bVarsFromForm? $_REQUEST["HELP_MESSAGE"][$arLanguage["LID"]]: $arUserField["HELP_MESSAGE"][$arLanguage["LID"]])?>"></td>
                        </tr>
                    <?endwhile?>
                </table>
            </td>
        </tr>
        <?if(is_object($obEnum)):
            ?>
            <tr class="heading">
                <td colspan="2"><?echo GetMessage("USER_TYPE_LIST_LABEL")?></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <table border="0" cellspacing="0" cellpadding="0" class="internal" id="list_table">
                        <tr class="heading">
                            <td><?=GetMessage("USER_TYPE_LIST_ID")?></td>
                            <td><?=GetMessage("USER_TYPE_LIST_XML_ID")?></td>
                            <td><?=GetMessage("USER_TYPE_LIST_VALUE")?></td>
                            <td><?=GetMessage("USER_TYPE_LIST_SORT")?></td>
                            <td><?=GetMessage("USER_TYPE_LIST_DEF")?></td>
                            <td><?=GetMessage("USER_TYPE_LIST_DEL")?></td>
                        </tr>
                        <?if($MULTIPLE=="N"):?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?=GetMessage("USER_TYPE_LIST_NO_DEF")?></td>
                                <td>&nbsp;</td>
                                <td><input type="radio" name="LIST[DEF][]" value="0"></td>
                                <td>&nbsp;</td>
                            </tr>
                        <?endif?>
                        <?
                        $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => $UF_ID));
                        while($arEnum = $rsEnum->GetNext()):

                            if($bVarsFromForm && is_array($_REQUEST['LIST'][$arEnum["ID"]]))
                                foreach($_REQUEST['LIST'][$arEnum["ID"]] as $key=>$val)
                                    $arEnum[$key] = htmlspecialcharsbx($val);
                            ?>
                            <tr>
                                <td><?=$arEnum["ID"]?></td>
                                <td><input type="text" name="LIST[<?=$arEnum["ID"]?>][XML_ID]" value="<?=$arEnum["XML_ID"]?>" size="15" maxlength="255"></td>
                                <td><input type="text" name="LIST[<?=$arEnum["ID"]?>][VALUE]" value="<?=$arEnum["VALUE"]?>" size="35" maxlength="255"></td>
                                <td><input type="text" name="LIST[<?=$arEnum["ID"]?>][SORT]" value="<?=$arEnum["SORT"]?>" size="5" maxlength="10"></td>
                                <td><input type="<?=($MULTIPLE=="Y"? "checkbox": "radio")?>" name="LIST[DEF][]" value="<?=$arEnum["ID"]?>" <?=($arEnum["DEF"]=="Y"? "checked": "")?>></td>
                                <td><input type="checkbox" name="LIST[<?=$arEnum["ID"]?>][DEL]" value="Y"<?if($arEnum["DEL"] == "Y") echo " checked"?>></td>
                            </tr>
                        <?
                        endwhile;
                        ?>
                        <?
                        if($bVarsFromForm):
                            $n = 0;
                            foreach($_REQUEST['LIST'] as $key=>$val):
                                if(strncmp($key, "n", 1)===0):
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><input type="text" name="LIST[n<?=$n?>][XML_ID]" value="<?=htmlspecialcharsbx($val["XML_ID"])?>" size="15" maxlength="255"></td>
                                        <td><input type="text" name="LIST[n<?=$n?>][VALUE]" value="<?=htmlspecialcharsbx($val["VALUE"])?>" size="35" maxlength="255"></td>
                                        <td><input type="text" name="LIST[n<?=$n?>][SORT]" value="<?=htmlspecialcharsbx($val["SORT"])?>" size="5" maxlength="10"></td>
                                        <td><input type="<?=($MULTIPLE=="Y"? "checkbox": "radio")?>" name="LIST[DEF][]" value="n<?=$n?>"></td>
                                        <td><input type="checkbox" name="LIST[n<?=$n?>][DEL]" value="Y"<?if($val["DEL"] == "Y") echo " checked"?>></td>
                                    </tr>
                                    <?
                                    $n++;
                                endif;
                            endforeach;
                        else:
                            ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="text" name="LIST[n0][XML_ID]" value="" size="15" maxlength="255"></td>
                                <td><input type="text" name="LIST[n0][VALUE]" value="" size="35" maxlength="255"></td>
                                <td><input type="text" name="LIST[n0][SORT]" value="500" size="5" maxlength="10"></td>
                                <td><input type="<?=($MULTIPLE=="Y"? "checkbox": "radio")?>" name="LIST[DEF][]" value="n0"></td>
                                <td><input type="checkbox" name="LIST[n0][DEL]" value="Y"></td>
                            </tr>
                        <?
                        endif;
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="button" value="<?=GetMessage("USER_TYPE_LIST_MORE")?>" OnClick="addNewRow('list_table')" ></td>
            </tr>
        <?endif?>
    </tbody>
</table>
<?echo bitrix_sessid_post();?>
<?if($UF_ID>0):?>
    <input type="hidden" name="UF_ID" value="<?=$UF_ID?>">
<?endif;?>
<?if($ID>0):?>
    <input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<input type="hidden" name="back_url" value="<?=htmlspecialcharsbx($back_url)?>">
<input type="hidden" name="list_url" value="<?=htmlspecialcharsbx($list_url)?>">
<input type="hidden" name="FIELD_EDIT[SAVE]" value="Y">
<?

$popupWindow->EndContent();

$popupWindow->ShowStandardButtons();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
