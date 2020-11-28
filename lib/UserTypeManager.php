<?php

namespace Fi1a\UserSettings;

/**
 * Данный класс фактически является интерфейсной прослойкой между значениями
 * пользовательских свойств и сущностью к которой они привязаны.
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
     * @param bool $bVarsFromForm
     * @param mixed $form_value
     * @param array $arUserField
     *
     * @return string
     */
    function GetEditFormHTML($bVarsFromForm, $form_value, $arUserField)
    {
        if($arUserField["USER_TYPE"])
        {
            if(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml")))
            {
                $js = $this->ShowScript();

                if(!$bVarsFromForm) {
                    $form_value = $arUserField["VALUE"];
                } elseif($arUserField["USER_TYPE"]["BASE_TYPE"]=="file") {
                    $form_value = $GLOBALS[$arUserField["FIELD_NAME"] . "_old_id"];
                } elseif($arUserField["EDIT_IN_LIST"]=="N") {
                    $form_value = $arUserField["VALUE"];
                }

                if($arUserField["MULTIPLE"] == "N")
                {
                    $valign = "";
                    $rowClass = "";
                    $html = call_user_func_array(
                        array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
                        array(
                            $arUserField,
                            array(
                                "NAME" => $arUserField["FIELD_NAME"],
                                "VALUE" => (is_array($form_value)? $form_value : \htmlspecialcharsbx($form_value)),
                                "VALIGN" => &$valign,
                                "ROWCLASS" => &$rowClass
                            ),
                        )
                    );
                    return $html . $js;
                }
                elseif(is_callable(array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtmlmulty")))
                {
                    if(!is_array($form_value))
                    {
                        $form_value = array();
                    }
                    foreach($form_value as $key => $value)
                    {
                        if(!is_array($value))
                        {
                            $form_value[$key] = \htmlspecialcharsbx($value);
                        }
                    }

                    $rowClass = "";
                    $html = call_user_func_array(
                        array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtmlmulty"),
                        array(
                            $arUserField,
                            array(
                                "NAME" => $arUserField["FIELD_NAME"]."[]",
                                "VALUE" => $form_value,
                                "ROWCLASS" => &$rowClass
                            ),
                        )
                    );
                    return  $html . $js;
                }
                else
                {
                    if(!is_array($form_value))
                    {
                        $form_value = array();
                    }
                    $html = "";
                    $i = -1;
                    foreach($form_value as $i => $value)
                    {

                        if(
                            (is_array($value) && (strlen(implode("", $value)) > 0))
                            || ((!is_array($value)) && (strlen($value) > 0))
                        )
                        {
                            $html .= '<tr><td>'.call_user_func_array(
                                    array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
                                    array(
                                        $arUserField,
                                        array(
                                            "NAME" => $arUserField["FIELD_NAME"]."[".$i."]",
                                            "VALUE" => (is_array($value)? $value : \htmlspecialcharsbx($value)),
                                        ),
                                    )
                                ).'</td></tr>';
                        }
                    }
                    //Add multiple values support
                    $rowClass = "";
                    $FIELD_NAME_X = str_replace('_', 'x', $arUserField["FIELD_NAME"]);
                    $fieldHtml = call_user_func_array(
                        array($arUserField["USER_TYPE"]["CLASS_NAME"], "geteditformhtml"),
                        array(
                            $arUserField,
                            array(
                                "NAME" => $arUserField["FIELD_NAME"]."[".($i+1)."]",
                                "VALUE" => "",
                                "ROWCLASS" => &$rowClass
                            ),
                        )
                    );
                    return
                        '<table id="table_'.$arUserField["FIELD_NAME"].'">'.$html.'<tr><td>'.$fieldHtml.'</td></tr>'.
                        '<tr><td style="padding-top: 6px;"><input type="button" value="'.GetMessage("USER_TYPE_PROP_ADD").'" onClick="addNewRow(\'table_'.$arUserField["FIELD_NAME"].'\', \''.$FIELD_NAME_X.'|'.$arUserField["FIELD_NAME"].'|'.$arUserField["FIELD_NAME"].'_old_id\')"></td></tr>'.
                        "<script type=\"text/javascript\">BX.addCustomEvent('onAutoSaveRestore', function(ob, data) {for (var i in data){if (i.substring(0,".(strlen($arUserField['FIELD_NAME'])+1).")=='".\CUtil::JSEscape($arUserField['FIELD_NAME'])."['){".
                        'addNewRow(\'table_'.$arUserField["FIELD_NAME"].'\', \''.$FIELD_NAME_X.'|'.$arUserField["FIELD_NAME"].'|'.$arUserField["FIELD_NAME"].'_old_id\')'.
                        "}}})</script>".
                        '</table>'.
                        $js;
                }
            }
        }
        return '';
    }
}
