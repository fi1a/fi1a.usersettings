<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\ParameterDictionary;
use Fi1a\UserSettings\FieldMapper;
use Fi1a\UserSettings\Helpers\Flush;
use Fi1a\UserSettings\FieldCollectionInterface;
use Fi1a\UserSettings\OptionInterface;
use Fi1a\UserSettings\TabCollectionInterface;
use Fi1a\UserSettings\Option;
use Fi1a\UserSettings\TabMapper;
use Fi1a\UserSettings\UserTypeManager;

/**
 * Класс компонента вывода пользовательских настроек
 */
class Fi1aUserSettingsAdminComponent extends CBitrixComponent
{

    const MODULE_ID = 'fi1a.usersettings';

    /**
     * @var OptionInterface
     */
    protected $option = null;

    /**
     * @inheritDoc
     */
    public function onPrepareComponentParams($arParams)
    {
        global $APPLICATION;

        if (!$arParams['RIGHT']) {
            $arParams['RIGHT'] = $APPLICATION->GetGroupRight(static::MODULE_ID);
        }

        if (!$arParams['LANGUAGE_ID']) {
            $arParams['LANGUAGE_ID'] = LANGUAGE_ID;
        }

        $arParams['VALUE_ID'] = (int)$arParams['VALUE_ID'];
        if (!$arParams['VALUE_ID']) {
            $arParams['VALUE_ID'] = 1;
        }

        return $arParams;
    }

    /**
     * @inheritDoc
     */
    public function executeComponent()
    {
        $this->arResult = [
            'STATUS' => '',
            'ERRORS' => [],
            'TABS' => [],
            'FIELDS' => [],
            'VARS_FROM_FORM' => false,
        ];
        $moduleMode = Loader::includeSharewareModule(static::MODULE_ID);

        // Проверка установлен модуль или нет
        if (!in_array($moduleMode, [Loader::MODULE_DEMO, Loader::MODULE_INSTALLED])) {
            $this->arResult['STATUS'] = 'ERROR';
            $this->arResult['ERRORS'][] = Loc::getMessage('FUS_MODULE_NOT_INSTALL');

            $this->IncludeComponentTemplate();

            return;
        }

        // Проверка прав
        if ($this->arParams['RIGHT'] < 'E') {
            $this->arResult['STATUS'] = 'ERROR';
            $this->arResult['ERRORS'][] = Loc::getMessage('FUS_NO_RIGHTS');

            $this->IncludeComponentTemplate();

            return;
        }

        if (Flush::get('FUS_SAVE_OPTION_SUCCESS')) {
            $this->arResult['STATUS'] = 'SUCCESS';
        }

        $this->option = Option::getInstance();
        $this->arResult['TABS'] = $this->getTabs();
        $this->arResult['FIELDS'] = $this->getFields();

        $values = $this->option->getAll();
        foreach ($this->arResult['FIELDS'] as $field) {
            $field['UF']['VALUE'] = $values[$field['UF']['FIELD_NAME']];
            $field['UF']['ENTITY_VALUE_ID'] = $this->option::ID;
        }

        $request = Application::getInstance()->getContext()->getRequest();

        // Сохранение значений
        if ($request->isPost() && ($request->getPost('save') || $request->getPost('apply'))) {
            if (!check_bitrix_sessid()) {
                $this->arResult['ERRORS'][] = Loc::getMessage('FUS_SESSION_EXPIRED');
            } elseif ($this->arParams['RIGHT'] < 'F') {
                $this->arResult['ERRORS'][] = Loc::getMessage('FUS_NO_RIGHTS_FOR_CHANGE');
            }

            if (empty($this->arResult['ERRORS'])) {
                $this->handleForm($request->getPostList(), $request->getFileList());

                if (empty($this->arResult['ERRORS'])) {
                    Flush::set('FUS_SAVE_OPTION_SUCCESS', true);

                    \LocalRedirect($request->getRequestUri());
                }
            }
        }

        $this->IncludeComponentTemplate();
    }

    /**
     * Обработка сохранения формы
     *
     * @param ParameterDictionary $post
     * @param ParameterDictionary $files
     */
    protected function handleForm(ParameterDictionary $post, ParameterDictionary $files)
    {
        $userTypeManager = UserTypeManager::getInstance();

        $this->arResult['FORM'] = [];

        $userTypeManager->EditFormAddFields(
            $this->option::ENTITY_ID,
            $this->arResult['FORM'],
            [
                'FILES' => $files->toArray(),
                'FORM' => $post->toArray(),
            ]
        );

        $this->arResult['VARS_FROM_FORM'] = true;

        foreach ($this->arResult['FORM'] as $key => $value) {
            $result = $this->option->set($key, $value);

            if (!$result->isSuccess()) {
                $this->arResult['ERRORS'] = array_merge($this->arResult['ERRORS'], $result->getErrorMessages());
            }
        }
        unset($key, $value);
    }

    /**
     * Возвращает коллекцию табов
     *
     * @return TabCollectionInterface
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getTabs(): TabCollectionInterface
    {
        return TabMapper::getActive([
            'order' => ['SORT' => 'ASC',]
        ]);
    }

    /**
     * Возвращает коллекцию полей
     *
     * @return FieldCollectionInterface
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getFields(): FieldCollectionInterface
    {
        return FieldMapper::getActive();
    }
}
