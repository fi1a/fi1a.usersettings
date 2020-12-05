<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use CUtil;
use Fi1a\UserSettings\Helpers\ModuleRegistry;

use function htmlspecialcharsbx;

/**
 * Данный класс фактически является интерфейсной прослойкой между значениями
 * пользовательских свойств и сущностью к которой они привязаны.
 *
 * @codeCoverageIgnore
 */
class UserTypeManager extends \CUserTypeManager
{
    /**
     * Синглетон
     *
     * @return $this
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Возвращает поле для редактирования значения пользовательского поля
     *
     * @param mixed $formValue
     * @param string[] $arUserField
     *
     * @return string
     */
    public function getEditFormHTML(bool $bVarsFromForm, $formValue, array $arUserField)
    {
        if ($arUserField['USER_TYPE']) {
            if (is_callable([$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtml'])) {
                $js = $this->ShowScript();

                if (!$bVarsFromForm) {
                    $formValue = $arUserField['VALUE'];
                } elseif ($arUserField['USER_TYPE']['BASE_TYPE'] === 'file') {
                    $formValue = ModuleRegistry::getGlobals($arUserField['FIELD_NAME'] . '_old_id');
                } elseif ($arUserField['EDIT_IN_LIST'] === 'N') {
                    $formValue = $arUserField['VALUE'];
                }

                if ($bVarsFromForm) {
                    $arUserField['VALUE'] = $formValue;
                }

                if (
                    $arUserField['MULTIPLE'] === 'N' ||
                    $arUserField['USER_TYPE']['USE_FIELD_COMPONENT']
                ) {
                    $valign = '';
                    $rowClass = '';
                    $html = call_user_func_array(
                        [$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtml'],
                        [
                            $arUserField,
                            [
                                'NAME' => $arUserField['FIELD_NAME'],
                                'VALUE' => (is_array($formValue) ? $formValue : htmlspecialcharsbx($formValue)),
                                'VALIGN' => &$valign,
                                'ROWCLASS' => &$rowClass,
                            ],
                        ]
                    );

                    return $html . $js;
                } elseif (is_callable([$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtmlmulty'])) {
                    if (!is_array($formValue)) {
                        $formValue = [];
                    }
                    foreach ($formValue as $key => $value) {
                        if (!is_array($value)) {
                            $formValue[$key] = htmlspecialcharsbx($value);
                        }
                    }

                    $rowClass = '';
                    $html = call_user_func_array(
                        [$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtmlmulty'],
                        [
                            $arUserField,
                            [
                                'NAME' => $arUserField['FIELD_NAME'] . '[]',
                                'VALUE' => $formValue,
                                'ROWCLASS' => &$rowClass,
                            ],
                        ]
                    );

                    return $html . $js;
                } else {
                    if (!is_array($formValue)) {
                        $formValue = [];
                    }
                    $html = '';
                    $i = -1;
                    foreach ($formValue as $i => $value) {
                        if (
                            (is_array($value) && (strlen(implode('', $value)) > 0))
                            || ((!is_array($value)) && (strlen((string) $value) > 0))
                        ) {
                            $html .= '<tr><td>' . call_user_func_array(
                                [$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtml'],
                                [
                                    $arUserField,
                                    [
                                        'NAME' => $arUserField['FIELD_NAME'] . '[' . $i . ']',
                                        'VALUE' => (is_array($value) ? $value : htmlspecialcharsbx($value)),
                                    ],
                                ]
                            ) . '</td></tr>';
                        }
                    }
                    //Add multiple values support
                    $rowClass = '';
                    $fieldNameX = str_replace('_', 'x', $arUserField['FIELD_NAME']);
                    $fieldHtml = call_user_func_array(
                        [$arUserField['USER_TYPE']['CLASS_NAME'], 'geteditformhtml'],
                        [
                            $arUserField,
                            [
                                'NAME' => $arUserField['FIELD_NAME'] . '[' . ($i + 1) . ']',
                                'VALUE' => '',
                                'ROWCLASS' => &$rowClass,
                            ],
                        ]
                    );

                    return '<table id="table_' . $arUserField['FIELD_NAME'] . '">' . $html . '<tr><td>'
                        . $fieldHtml . '</td></tr>' .
                        '<tr><td style="padding-top: 6px;"><input type="button" value="'
                        . GetMessage('USER_TYPE_PROP_ADD') . '" onClick="addNewRow(\'table_'
                        . $arUserField['FIELD_NAME'] . '\', \'' . $fieldNameX . '|' . $arUserField['FIELD_NAME'] . '|'
                        . $arUserField['FIELD_NAME'] . '_old_id\')"></td></tr>' .
                        "<script type=\"text/javascript\">BX.addCustomEvent('onAutoSaveRestore', '
                        . 'function(ob, data) {for (var i in data){if (i.substring(0,"
                        . (strlen($arUserField['FIELD_NAME']) + 1) . ")=='"
                        . CUtil::JSEscape($arUserField['FIELD_NAME']) . "['){" .
                        'addNewRow(\'table_' . $arUserField['FIELD_NAME'] . '\', \'' . $fieldNameX . '|'
                        . $arUserField['FIELD_NAME'] . '|' . $arUserField['FIELD_NAME'] . '_old_id\')' .
                        '}}})</script>' .
                        '</table>' .
                        $js;
                }
            }
        }

        return '';
    }
}
