<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */

echo BeginNote();
?>
<?php if ($arParams['RIGHT'] >= 'R') { ?>
    <p><?= Loc::getMessage('FUS_CHANGE_TO')?> <a href="/bitrix/admin/settings.php?lang=<?= LANGUAGE_ID?>&mid=fi1a.usersettings&mid_menu=1"><?= Loc::getMessage('FUS_MODULE_SETTINGS')?></a>.</p>
<?php } ?>
<ul>
    <li><?= Loc::getMessage('FUS_LI_1')?></li>
    <li><?= Loc::getMessage('FUS_LI_2')?></li>
</ul>
<?php if ($arParams['RIGHT'] >= 'W') { ?>
    <p>
        <?= Loc::getMessage('FUS_ADD_TAB_OR_FIELD_1')?>
        <?php if (count($arResult['TABS'])) { ?>
            <?= Loc::getMessage('FUS_ADD_TAB_OR_FIELD_2')?>
        <?php } ?>
    </p>
<?php } ?>
<?php
echo EndNote();
