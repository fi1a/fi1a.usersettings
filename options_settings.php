<?php

namespace Fi1a\UserSettings;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/**
 * @var array $fusTabs
 * @var array $fusFields
 * @var array $sites
 * @var array $languages
 * @var string $rightForModule
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

$json = [
    'tabs' => array_values($fusTabs),
    'fields' => array_values($fusFields),
    'languages' => $languages,
    'loc' => [
        'DELETE' => Loc::getMessage('FUS_DELETE'),
        'DELETE_CONFIRM' => Loc::getMessage('FUS_DELETE_CONFIRM'),
        'EDIT' => Loc::getMessage('FUS_EDIT'),
    ],
];

?>
<tr>
    <td valign="top" width="100%">
        <div id="fi1a-usersettings-options" data-object="<?= \htmlspecialcharsbx(Json::encode($json))?>">
            <input<?php if ($rightForModule < 'W') { ?> disabled<?php } ?> type="button" v-on:click="addTab()" v-if="tabs.length" value="<?= Loc::getMessage('FUS_ADD_TAB_BUTTON')?>" />
            <input<?php if ($rightForModule < 'W') { ?> disabled<?php } ?> type="button" v-on:click="addField()" v-if="tabs.length" value="<?= Loc::getMessage('FUS_ADD_FIELD_BUTTON')?>" />
            <br/><br/>
            <p v-if="!tabs.length"><i><?= Loc::getMessage('FUS_EMPTY_LIST')?></i></p>
            <transition-group name="flip-list" tag="table" class="internal m_fus" v-if="tabs.length">
                <template v-for="tab in sortedTabs">
                    <tbody :key="tab.ID">
                        <tr class="heading m_fus">
                            <?php if ($rightForModule >= 'W') { ?>
                                <td></td>
                            <?php } ?>
                            <td><?= Loc::getMessage('FUS_LIST_OBJECT')?></td>
                            <td>ID</td>
                            <td><?= Loc::getMessage('FUS_LIST_ACTIVE')?></td>
                            <td><?= Loc::getMessage('FUS_LIST_CODE')?></td>
                            <td><?= Loc::getMessage('FUS_LIST_LANGUAGE')?></td>
                            <td><?= Loc::getMessage('FUS_LIST_NAME')?></td>
                            <td><?= Loc::getMessage('FUS_LIST_TITLE')?></td>
                            <td><?= Loc::getMessage('FUS_LIST_SORT')?></td>
                        </tr>
                        <tr
                        <?php if ($rightForModule >= 'W') { ?>
                            :oncontextmenu="tabContextMenu(tab)"
                        <?php } ?>
                        >
                            <?php if ($rightForModule >= 'W') { ?>
                                <td :rowspan="languages.length" onclick="BX.adminList.ShowMenu(this.firstChild, this.parentNode.oncontextmenu(), this.parentNode);"><div class="adm-list-table-popup" title="<?= Loc::getMessage('FUS_ACTION')?>"></div></td>
                            <?php } ?>
                            <td :rowspan="languages.length"><?= Loc::getMessage('FUS_OBJECT_TYPE_TAB')?></td>
                            <td :rowspan="languages.length">
                                {{tab.ID}}
                                <input type="hidden" :value="tab.ID" :name="'TABS[' + tab.ID + '][ID]'" />
                            </td>
                            <td :rowspan="languages.length">
                                <input type="checkbox" :name="'TABS[' + tab.ID + '][ACTIVE]'" :id="'tab-active' + tab.ID" value="1" v-bind:true-value="1" v-bind:false-value="0" v-model="tab.ACTIVE" class="adm-designed-checkbox">
                                <label class="adm-designed-checkbox-label" :for="'tab-active' + tab.ID" title=""></label>
                            </td>
                            <td :rowspan="languages.length"><input :name="'TABS[' + tab.ID + '][CODE]'" v-model="tab.CODE" size="25" type="text" /></td>
                            <td>{{languages[0].LID}}</td>
                            <td><input size="25" type="text" :name="'TABS[' + tab.ID + '][LOCALIZATION][' + languages[0].LID + '][L_NAME]'" v-model="tab.LOCALIZATION[languages[0].LID].L_NAME" /></td>
                            <td><input size="25" type="text" :name="'TABS[' + tab.ID + '][LOCALIZATION][' + languages[0].LID + '][L_TITLE]'" v-model="tab.LOCALIZATION[languages[0].LID].L_TITLE" /></td>
                            <td :rowspan="languages.length"><input :name="'TABS[' + tab.ID + '][SORT]'" :value="tab.SORT" v-on:change="tab.SORT = $event.target.value" size="8" type="text" value="500" /></td>
                        </tr>
                        <tr v-for="(language, index) in languages" v-if="index > 0">
                            <td>{{language.LID}}</td>
                            <td><input size="25" type="text" :name="'TABS[' + tab.ID + '][LOCALIZATION][' + language.LID + '][L_NAME]'" v-model="tab.LOCALIZATION[language.LID].L_NAME" /></td>
                            <td><input size="25" type="text" :name="'TABS[' + tab.ID + '][LOCALIZATION][' + language.LID + '][L_TITLE]'" v-model="tab.LOCALIZATION[language.LID].L_TITLE" /></td>
                        </tr>
                        <tr v-if="getFieldsCount(tab.ID) > 0">
                            <td colspan="<?php if ($rightForModule >= 'W') { ?>9<?php } else { ?>8<?php } ?>" class="b-fus-field-container">
                                <transition-group name="flip-list" tag="table" class="internal m_fus">
                                    <template
                                        v-for="field in getFields(tab.ID)"
                                    >
                                        <tbody :key="field.ID">
                                            <tr class="heading m_fus">
                                                <?php if ($rightForModule >= 'W') { ?>
                                                    <td></td>
                                                <?php } ?>
                                                <td><?= Loc::getMessage('FUS_LIST_OBJECT')?></td>
                                                <td>ID</td>
                                                <td><?= Loc::getMessage('FUS_LIST_ACTIVE')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_CODE')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_FIELD_TYPE')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_LANGUAGE')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_NAME')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_HELP')?></td>
                                                <td><?= Loc::getMessage('FUS_LIST_SORT')?></td>
                                            </tr>
                                            <tr
                                            <?php if ($rightForModule >= 'W') { ?>
                                                :oncontextmenu="fieldContextMenu(field)"
                                            <?php } ?>
                                            >
                                                <?php if ($rightForModule >= 'W') { ?>
                                                    <td :rowspan="languages.length" onclick="BX.adminList.ShowMenu(this.firstChild, this.parentNode.oncontextmenu(), this.parentNode);"><div class="adm-list-table-popup" title="<?= Loc::getMessage('FUS_ACTION')?>"></div></td>
                                                <?php } ?>
                                                <td :rowspan="languages.length"><?= Loc::getMessage('FUS_OBJECT_TYPE_FIELD')?></td>
                                                <td :rowspan="languages.length">
                                                    {{field.ID}}
                                                    <input type="hidden" :value="field.ID" :name="'FIELDS[' + field.ID + '][ID]'" />
                                                    <input type="hidden" :value="field.UF.ID" :name="'FIELDS[' + field.ID + '][UF][ID]'" />
                                                </td>
                                                <td :rowspan="languages.length">
                                                    <input type="checkbox" :name="'FIELDS[' + field.ID + '][ACTIVE]'" :id="'field-active' + field.ID" value="1" v-bind:true-value="1" v-bind:false-value="0" v-model="field.ACTIVE" class="adm-designed-checkbox">
                                                    <label class="adm-designed-checkbox-label" :for="'field-active' + field.ID" title=""></label>
                                                </td>
                                                <td :rowspan="languages.length">{{field.UF.FIELD_NAME}}</td>
                                                <td :rowspan="languages.length">{{getUfDescription(field.UF.USER_TYPE_ID, field.UF.USER_TYPE)}}</td>
                                                <td>{{languages[0].LID}}</td>
                                                <td><input size="25" type="text" :name="'FIELDS[' + field.ID + '][UF][EDIT_FORM_LABEL][' + languages[0].LID + ']'" v-model="field.UF.EDIT_FORM_LABEL[languages[0].LID]"/></td>
                                                <td><input size="25" type="text" :name="'FIELDS[' + field.ID + '][UF][HELP_MESSAGE][' + languages[0].LID + ']'" v-model="field.UF.HELP_MESSAGE[languages[0].LID]"/></td>
                                                <td :rowspan="languages.length"><input :name="'FIELDS[' + field.ID + '][UF][SORT]'" :value="field.UF.SORT" v-on:change="field.UF.SORT = $event.target.value" size="8" type="text" /></td>
                                            </tr>
                                            <tr v-for="(language, index) in languages" v-if="index > 0">
                                                <td>{{language.LID}}</td>
                                                <td><input size="25" type="text" :name="'FIELDS[' + field.ID + '][UF][EDIT_FORM_LABEL][' + language.LID + ']'" v-model="field.UF.EDIT_FORM_LABEL[language.LID]"/></td>
                                                <td><input size="25" type="text" :name="'FIELDS[' + field.ID + '][UF][HELP_MESSAGE][' + language.LID + ']'" v-model="field.UF.HELP_MESSAGE[language.LID]"/></td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </transition-group>
                            </td>
                        </tr>
                    </tbody>
                </template>
            </transition-group>
            <br/>
            <input<?php if ($rightForModule < 'W') { ?> disabled<?php } ?> type="button" v-on:click="addTab()" value="<?= Loc::getMessage('FUS_ADD_TAB_BUTTON')?>" />
            <input<?php if ($rightForModule < 'W') { ?> disabled<?php } ?> type="button" v-on:click="addField()" v-if="tabs.length" value="<?= Loc::getMessage('FUS_ADD_FIELD_BUTTON')?>" />
        </div>
    </td>
</tr>

