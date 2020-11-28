<?php

namespace Fi1a\UserSettings;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var array $languages
 * @var array $allowParentMenu
 * @var array $menu
 */

use Bitrix\Main\Localization\Loc;

?>
<tr>
    <td valign="top" width="100%">
        <table class="adm-detail-content-table edit-table">
            <tbody>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
                        <label for="fus-parent-menu"><?= Loc::getMessage('FUS_PARENT_MENU')?>:</label>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <select class="b-fus-select" id="fus-parent-menu" name="MENU[PARENT_MENU]">
                            <?php foreach ($allowParentMenu as $parentMenu => $parentMenuTitle) { ?>
                                <option<?php if ($parentMenu == $menu['PARENT_MENU']) { ?> selected<?php } ?> value="<?= $parentMenu?>"><?= $parentMenuTitle?></option>
                            <?php } ?>
                            <?php
                            unset($parentMenu, $parentMenuTitle);
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="adm-detail-content-cell-l">
                        <label for="fus-sort"><?= Loc::getMessage('FUS_LIST_SORT')?>:</label>
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <input type="text" id="fus-sort" name="MENU[SORT]" size="6" value="<?= (int)$menu['SORT']?>">
                    </td>
                </tr>
                <tr>
                    <td class="adm-detail-content-cell-l">
                        <?= Loc::getMessage('FUS_MENU_TEXT')?>:
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <table class="b-fus-table">
                            <tbody>
                                <?php foreach ($languages as $language) { ?>
                                    <tr>
                                        <td style="width: 5px;"><?= $language['LID']?></td>
                                        <td><input class="b-fus-field" type="text" name="MENU[LOCALIZATION][<?= $language['LID']?>][MENU_TEXT]" value="<?= \htmlspecialcharsbx($menu['LOCALIZATION'][$language['LID']]['MENU_TEXT'])?>"></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                unset($language);
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="adm-detail-content-cell-l">
                        <?= Loc::getMessage('FUS_MENU_TITLE')?>:
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <table class="b-fus-table">
                            <tbody>
                                <?php foreach ($languages as $language) { ?>
                                    <tr>
                                        <td style="width: 5px;"><?= $language['LID']?></td>
                                        <td><input class="b-fus-field" type="text" name="MENU[LOCALIZATION][<?= $language['LID']?>][MENU_TITLE]" value="<?= \htmlspecialcharsbx($menu['LOCALIZATION'][$language['LID']]['MENU_TITLE'])?>"></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                unset($language);
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="adm-detail-content-cell-l">
                        <?= Loc::getMessage('FUS_PAGE_TITLE')?>:
                    </td>
                    <td class="adm-detail-content-cell-r">
                        <table class="b-fus-table">
                            <tbody>
                                <?php foreach ($languages as $language) { ?>
                                    <tr>
                                        <td style="width: 5px;"><?= $language['LID']?></td>
                                        <td><input class="b-fus-field" type="text" name="MENU[LOCALIZATION][<?= $language['LID']?>][PAGE_TITLE]" value="<?= \htmlspecialcharsbx($menu['LOCALIZATION'][$language['LID']]['PAGE_TITLE'])?>"></td>
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
    </td>
</tr>
<?php
