<?php

declare(strict_types=1);

namespace Fi1a\Installers\Fi1aUsersettings;

use Bitrix\Main\Config\Option;
use CModule;
use ErrorException;
use Fi1a\Console\IO\InputInterface;
use Fi1a\Console\IO\OutputInterface;
use Fi1a\Installers\AbstractLibrary;
use Fi1a\Installers\Version;
use Fi1a\Installers\VersionInterface;

/**
 * Библиотека
 */
class Library extends AbstractLibrary
{
    public const MODULE_ID = 'fi1a.usersettings';

    /**
     * @inheritDoc
     */
    public function __construct(OutputInterface $output, InputInterface $stream)
    {
        parent::__construct($output, $stream);
        $this->includeBitrix();
    }

    /**
     * @inheritDoc
     */
    public function canInstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canUninstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update(): bool
    {
        /**
         * @var \fi1a_usersettings|false $module
         */
        $module = CModule::CreateModuleObject(self::MODULE_ID);
        if ($module) {
            Option::set(self::MODULE_ID, 'version', (string) $module->MODULE_VERSION);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentVersion(): VersionInterface
    {
        [$major, $minor, $build] = explode(
            '.',
            (string) Option::get(self::MODULE_ID, 'version', '1.0.0')
        );

        return new Version((int) $major, (int) $minor, (int) $build);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateVersion(): VersionInterface
    {
        /**
         * @var \fi1a_usersettings|false $module
         */
        $module = CModule::CreateModuleObject(self::MODULE_ID);
        if (!$module) {
            throw new ErrorException(sprintf('Модуль "%s" не найден', self::MODULE_ID));
        }
        [$major, $minor, $build] = explode(
            '.',
            (string) $module->MODULE_VERSION
        );

        return new Version((int) $major, (int) $minor, (int) $build);
    }

    /**
     * Подключить битрикс
     */
    private function includeBitrix(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../..');

        defined('NO_KEEP_STATISTIC') || define('NO_KEEP_STATISTIC', true);
        defined('NOT_CHECK_PERMISSIONS') || define('NOT_CHECK_PERMISSIONS', true);
        defined('BX_WITH_ON_AFTER_EPILOG') || define('BX_WITH_ON_AFTER_EPILOG', true);
        defined('BX_NO_ACCELERATOR_RESET') || define('BX_NO_ACCELERATOR_RESET', true);

        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
    }
}
