<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\SprintMigration\Builders;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Fi1a\UserSettings\SprintMigration\Helpers\UserSettingsHelper;
use Sprint\Migration\VersionBuilder;

/**
 * Менеджер модуля пользовательских настроек
 *
 * @codeCoverageIgnore
 */
class UserSettingsBuilder extends VersionBuilder
{
    /**
     * Активность
     */
    protected function isBuilderEnabled(): bool
    {
        return Loader::includeModule('fi1a.usersettings');
    }

    /**
     * Инициализация
     */
    protected function initialize(): void
    {
        $this->setTitle(Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_TITLE'));
        $this->setGroup('Tools');

        $this->addVersionFields();
    }

    /**
     * Выполнение
     */
    protected function execute(): void
    {
        $helper = $this->getHelperManager();
        /**
         * @var UserSettingsHelper $userSettingsHelper
         */
        $userSettingsHelper = $helper->UserSettings();

        $what = $this->addFieldAndReturn(
            'what',
            [
                'title' => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT'),
                'placeholder' => '',
                'width' => 250,
                'multiple' => true,
                'select' => [
                    [
                        'title' => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT_TABS'),
                        'value' => 'tabs',
                    ],
                    [
                        'title' => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT_FIELDS'),
                        'value' => 'fields',
                    ],
                    [
                        'title' => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT_OPTIONS'),
                        'value' => 'options',
                    ],
                ],
            ]
        );

        if (!is_array($what) || !count($what)) {
            $this->rebuildField('what');
        }

        $tabIdEnums = [];
        foreach ($userSettingsHelper->getTabs() as $tab) {
            $tabIdEnums[] = [
                'title' => $tab['LOCALIZATION'][LANGUAGE_ID]['L_NAME']
                    . ($tab['LOCALIZATION'][LANGUAGE_ID]['L_NAME'] ? ' ' : '')
                    . '(' . $tab['CODE'] . ')',
                'value' => $tab['ID'],
            ];
        }

        $tabId = (int) $this->addFieldAndReturn(
            'tab_id',
            [
                'title' => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT_TABS'),
                'placeholder' => '',
                'width' => 250,
                'select' => $tabIdEnums,
            ]
        );

        if (!$tabId) {
            $this->rebuildField('tab_ids');
        }

        if (in_array('fields', $what) || in_array('options', $what)) {
            $fieldIdsEnums = [];
            foreach ($userSettingsHelper->getFields(['TAB_ID' => $tabId]) as $field) {
                $fieldIdsEnums[] = [
                    'title' => $field['UF']['EDIT_FORM_LABEL'][LANGUAGE_ID]
                        . ($field['UF']['EDIT_FORM_LABEL'][LANGUAGE_ID] ? ' ' : '')
                        . '(' . $field['UF']['FIELD_NAME'] . ')',
                    'value' => $field['ID'],
                ];
            }
            $fieldIds = $this->addFieldAndReturn(
                'field_ids',
                [
                    'title'       => Loc::getMessage('FUS_SPRINT_MIGRATION_BUILDER_WHAT_FIELDS'),
                    'placeholder' => '',
                    'width'       => 250,
                    'multiple' => true,
                    'select'      => $fieldIdsEnums,
                ]
            );

            if (!is_array($fieldIds) || !count($fieldIds)) {
                $this->rebuildField('field_ids');
            }
        }

        $tabExport = [];
        if (in_array('tabs', $what)) {
            $tabExport = $userSettingsHelper->exportTab((int) $tabId);

            if (!$tabExport) {
                $this->rebuildField('tab_id');
            }
        }

        $fieldExports = [];
        if (in_array('fields', $what)) {
            foreach ($fieldIds as $fieldId) {
                $fieldExport = $userSettingsHelper->exportField((int) $fieldId);
                if ($fieldExport) {
                    $fieldExports[] = $fieldExport;
                }
            }

            if (!count($fieldExports)) {
                $this->rebuildField('field_ids');
            }
        }

        $optionExports = [];
        if (in_array('options', $what)) {
            foreach ($fieldIds as $fieldId) {
                $field = $userSettingsHelper->getFieldById((int) $fieldId);
                if (!$field) {
                    continue;
                }
                $optionExports[$field['UF']['FIELD_NAME']] = $userSettingsHelper->getOption($field['UF']['FIELD_NAME']);
            }

            if (!count($optionExports)) {
                $this->rebuildField('field_ids');
            }
        }

        $this->createVersionFile(
            __DIR__ . '/../../../sprint.migration/templates/UserSettingsExport.php',
            [
                'optionExports' => $optionExports,
                'fieldExports' => $fieldExports,
                'tabExport' => $tabExport,
            ]
        );
    }
}
