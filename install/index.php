<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\IO\FileDeleteException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Fi1a\UserSettings\Internals\FieldsTable;
use Fi1a\UserSettings\Internals\TabsTable;

Loc::loadMessages(__FILE__);

/**
 * Инсталятор модуля
 */
class fi1a_usersettings extends CModule
{

    var $MODULE_NAME;
    var $MODULE_ID = 'fi1a.usersettings';
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS;

    /**
     * @var string
     */
    private $moduleDir = null;

    /**
     * @var string
     */
    private $bitrixAdminDir = null;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->initId();
        $this->initVersion();
        $this->initName();
        $this->initGroupRights();
        $this->initDescription();
        $this->initPartnerInfo();

        $this->moduleDir = $this->createPath(Application::getDocumentRoot(), 'local', 'modules', $this->MODULE_ID);
        if (!is_dir($this->moduleDir)) {
            $this->moduleDir = $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'modules', $this->MODULE_ID);
        }

        $this->bitrixAdminDir = $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'admin');
    }

    /**
     * Идентификатор модуля
     */
    private function initId()
    {
        $this->MODULE_ID = 'fi1a.usersettings';
    }

    /**
     * Дата и версия модуля
     */
    private function initVersion()
    {
        $arModuleVersion = [];

        include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    /**
     * Название модуля
     */
    private function initName()
    {
        $this->MODULE_NAME = Loc::getMessage('FUS_MODULE_NAME');
    }

    /**
     * Описание модуля
     */
    private function initDescription()
    {
        $this->MODULE_DESCRIPTION = Loc::getMessage('FUS_MODULE_DESCRIPTION');
    }

    /**
     * Информация о партнере
     */
    private function initPartnerInfo()
    {
        $this->PARTNER_NAME = Loc::getMessage('FUS_PARTNER_NAME');
        $this->PARTNER_URI = 'https://github.com/fi1a/fi1a.usersettings';
    }

    /**
     * Права модуля
     */
    protected function initGroupRights()
    {
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    /**
     * Установка
     */
    public function DoInstall()
    {
        if (!$this->InstallDB()) {
            return false;
        }

        if (!$this->InstallEvents()) {
            $this->UninstallDB();

            return false;
        }

        if (!$this->InstallFiles()) {
            $this->UninstallDB();
            $this->UninstallFiles();

            return false;
        }

        return true;
    }

    /**
     * Установка базы модуля
     *
     * @return bool
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     */
    public function InstallDB(): bool
    {
        global $APPLICATION;

        $connection = Application::getConnection();

        try {
            $connection->startTransaction();

            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);

            $this->createTabsTable($connection);
            $this->createFieldsTable($connection);
            $this->setSettings();

            $connection->commitTransaction();
        } catch (\Exception $e) {
            $connection->rollbackTransaction();

            $APPLICATION->ResetException();
            $APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Установка файлов модуля
     *
     * @return bool
     *
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function InstallFiles(): bool
    {
        global $APPLICATION;

        if (!is_writable($this->bitrixAdminDir)) {
            $APPLICATION->ResetException();
            $APPLICATION->ThrowException(Loc::getMessage('FUS_WRITE_ERROR_BITRIX_ADMIN_DIR'));

            return false;
        }

        if (!$this->copyAdminFiles()) {
            return false;
        }

        \CopyDirFiles(
            $this->createPath($this->moduleDir, 'install', 'themes', '.default'),
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'themes', '.default'),
            true,
            true
        );
        \CopyDirFiles(
            $this->createPath($this->moduleDir, 'install', 'js'),
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'js'),
            true,
            true
        );
        \CopyDirFiles(
            $this->createPath($this->moduleDir, 'install', 'components'),
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'components'),
            true,
            true
        );

        return true;
    }

    /**
     * Установка событий
     *
     * @return bool
     */
    public function InstallEvents(): bool
    {
        RegisterModuleDependences('sprint.migration', 'OnSearchConfigFiles', $this->MODULE_ID, \Fi1a\UserSettings\Events\SprintMigration::class, 'onSearchConfigFiles');

        return true;
    }

    /**
     * Удаление модуля
     */
    public function DoUninstall()
    {
        if (!$this->UnInstallDB()) {
            return false;
        }

        if (!$this->UnInstallEvents()) {
            $this->InstallDB();

            return false;
        }

        if (!$this->UnInstallFiles()) {
            $this->InstallDB();
            $this->InstallEvents();

            return false;
        }

        return true;
    }

    /**
     * Удаление базы
     *
     * @return bool
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function UnInstallDB(): bool
    {
        global $APPLICATION;

        $connection = Application::getConnection();

        try {
            Loader::includeModule($this->MODULE_ID);

            $connection->startTransaction();

            $this->dropTabsTable($connection);
            $this->dropFieldsTable($connection);
            $this->deleteFUserFields();
            $this->deleteSettings();

            ModuleManager::unRegisterModule($this->MODULE_ID);

            $connection->commitTransaction();
        } catch (\Exception $e) {
            $connection->rollbackTransaction();

            $APPLICATION->ResetException();
            $APPLICATION->ThrowException($e->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Удаление файлов
     *
     * @return bool
     *
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function UnInstallFiles(): bool
    {
        global $APPLICATION;

        if (!is_writable($this->bitrixAdminDir)) {
            $APPLICATION->ResetException();
            $APPLICATION->ThrowException(Loc::getMessage('FUS_WRITE_ERROR_BITRIX_ADMIN_DIR'));

            return false;
        }

        if (!$this->unlinkAdminFiles()) {
            return false;
        }
        if (!$this->unlinkComponents()) {
            return false;
        }

        \DeleteDirFiles(
            $this->createPath($this->moduleDir, 'install', 'themes', '.default'),
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'themes', '.default')
        );
        (new \Bitrix\Main\IO\Directory(
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'themes', '.default', 'icons', 'fi1a.usersettings')
        ))->delete();
        (new \Bitrix\Main\IO\Directory(
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'js', 'fi1a.usersettings')
        ))->delete();

        return true;
    }

    /**
     * Удаления событий
     *
     * @return bool
     */
    public function UnInstallEvents(): bool
    {
        UnRegisterModuleDependences('sprint.migration', 'OnSearchConfigFiles', $this->MODULE_ID, \Fi1a\UserSettings\Events\SprintMigration::class, 'onSearchConfigFiles');

        return true;
    }

    /**
     * Возвращает массив описывающий индивидуальную схему распределения прав модуля
     *
     * @return array
     */
    public function GetModuleRightList(): array
    {
        $rights = ['D', 'E', 'F', 'R', 'W',];
        $reference = [];

        foreach ($rights as $right) {
            $reference[] = '[' . $right . '] ' . Loc::getMessage('FUS_RIGHT_' . $right);
        }
        unset($right);

        return [
            'reference_id' => $rights,
            'reference' => $reference,
        ];
    }

    /**
     * Функция из кусочков создает полноценный путь с учетом системного разделителя папок
     *
     * @return string
     */
    protected function createPath()
    {
        $separator = DIRECTORY_SEPARATOR;
        $parts = func_get_args();

        return str_replace(
            [$separator, $separator . $separator, '//',],
            '/',
            (defined('PHP_WINDOWS_VERSION_PLATFORM') && PHP_WINDOWS_VERSION_PLATFORM ? '' : '/') . implode('/', $parts)
        );
    }

    /**
     * Функция из кусочков создает относительный путь с учетом системного разделителя папок
     *
     * @return string
     */
    protected function createRelativePath()
    {
        $separator = DIRECTORY_SEPARATOR;
        $parts = func_get_args();

        return str_replace([$separator, $separator . $separator, '//',], '/', '/' . implode('/', $parts));
    }

    /**
     * Создаем таблицу для вкладок
     *
     * @param Connection $connection
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    private function createTabsTable(Connection $connection)
    {
        $tableName = TabsTable::getTableName();
        if (!$connection->isTableExists($tableName)) {
            $connection->createTable(
                $tableName,
                TabsTable::getMap(),
                ['ID'],
                ['ID']
            );
        }
    }

    /**
     * Удаляем таблицу с вкладками
     *
     * @param Connection $connection
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    private function dropTabsTable(Connection $connection)
    {
        $tableName = TabsTable::getTableName();
        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    /**
     * Создаем таблицу для полей
     *
     * @param Connection $connection
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    private function createFieldsTable(Connection $connection)
    {
        $tableName = FieldsTable::getTableName();
        if (!$connection->isTableExists($tableName)) {
            $connection->createTable(
                $tableName,
                FieldsTable::getMap(),
                ['ID'],
                ['ID']
            );
        }
    }

    /**
     * Удаляем таблицу с полями
     *
     * @param Connection $connection
     *
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    private function dropFieldsTable(Connection $connection)
    {
        $tableName = FieldsTable::getTableName();
        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    /**
     * Удаление пользовательских полей
     */
    private function deleteFUserFields()
    {
        $userTypeEntity = new \CUserTypeEntity();

        $iterator = $userTypeEntity::GetList([], ['ENTITY_ID' => 'fus',]);
        while ($field = $iterator->fetch()) {
            $userTypeEntity->Delete($field['ID']);
        }
    }

    /**
     * Устанавливает настройки модуля
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function setSettings()
    {
        $languages = LanguageTable::getList([
            'order' => [
                'SORT' => 'ASC',
            ]
        ])->fetchAll();

        $localization = [];
        foreach ($languages as $language) {
            $localization[$language['LID']] = [
                'MENU_TEXT' => Loc::getMessage('FUS_MENU_TITLE'),
                'MENU_TITLE' => Loc::getMessage('FUS_MENU_TITLE'),
                'PAGE_TITLE' => Loc::getMessage('FUS_MENU_TITLE'),
            ];
        }

        Option::set($this->MODULE_ID, 'PARENT_MENU', 'global_menu_services');
        Option::set($this->MODULE_ID, 'SORT', 2000);
        Option::set($this->MODULE_ID, 'LOCALIZATION', serialize($localization));
        Option::set($this->MODULE_ID, 'version', $this->MODULE_VERSION);
    }

    /**
     * Удаляет настройки модуля
     *
     * @throws \Bitrix\Main\ArgumentNullException
     */
    private function deleteSettings()
    {
        Option::delete($this->MODULE_ID, []);
    }

    /**
     * Копирование файлов из папка admin модуля
     *
     * @return bool
     *
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    private function copyAdminFiles(): bool
    {
        global $APPLICATION;

        $relModuleDir = str_replace(Application::getDocumentRoot(), '', $this->moduleDir);

        $moduleAdminDir = new \Bitrix\Main\IO\Directory($this->createPath($this->moduleDir, 'admin'));
        foreach ($moduleAdminDir->getChildren() as $fileSystemEntry) {
            if (!$fileSystemEntry->isFile()) {
                continue;
            }

            /**
             * @var \Bitrix\Main\IO\File $fileSystemEntry
             */
            if ('php' != $fileSystemEntry->getExtension() || 'menu.php' == $fileSystemEntry->getName()) {
                continue;
            }

            $link = $this->createRelativePath($relModuleDir, 'admin', $fileSystemEntry->getName());
            $linkFileContents = '<?php' . PHP_EOL . 'require $_SERVER[\'DOCUMENT_ROOT\']."' . $link . '";' . PHP_EOL;
            $filePath = $this->createPath($this->bitrixAdminDir, $fileSystemEntry->getName());

            if (!file_put_contents($filePath, $linkFileContents)) {
                $APPLICATION->ResetException();
                $APPLICATION->ThrowException(Loc::getMessage('FUS_WRITE_FILE_ERROR', ['#FILE_PATH#' => $filePath]));

                return false;
            }
        }

        return true;
    }

    /**
     * Удаление файлов совпадающих с файлой в папке admin
     *
     * @return bool
     *
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    private function unlinkAdminFiles(): bool
    {
        global $APPLICATION;

        $moduleAdminDir = new \Bitrix\Main\IO\Directory($this->createPath($this->moduleDir, 'admin'));
        foreach ($moduleAdminDir->getChildren() as $fileSystemEntry) {
            if (!$fileSystemEntry->isFile()) {
                continue;
            }

            /**
             * @var \Bitrix\Main\IO\File $fileSystemEntry
             */
            if ('php' != $fileSystemEntry->getExtension() || 'menu.php' == $fileSystemEntry->getName()) {
                continue;
            }

            $filePath = $this->createPath($this->bitrixAdminDir, $fileSystemEntry->getName());
            if (!unlink($filePath)) {
                $APPLICATION->ResetException();
                $APPLICATION->ThrowException(Loc::getMessage('FUS_DELETE_FILE_ERROR', ['#FILE_PATH#' => $filePath]));

                return false;
            }
        }

        return true;
    }

    /**
     * Удаляет компоненты модуля
     *
     * @return bool
     *
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    private function unlinkComponents(): bool
    {
        global $APPLICATION;

        $moduleComponentDir = new \Bitrix\Main\IO\Directory(
            $this->createPath($this->moduleDir, 'install', 'components', 'fi1a')
        );

        foreach ($moduleComponentDir->getChildren() as $entry) {
            if (!$entry->isDirectory()) {
                continue;
            }

            try {
                (new \Bitrix\Main\IO\Directory(
                    $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'components', 'fi1a', $entry->getName())
                ))->delete();
            } catch (FileDeleteException $exception) {
                $APPLICATION->ResetException();
                $APPLICATION->ThrowException($exception->getMessage());

                return false;
            }

        }
        unset($entry);

        $componentDirectory = new \Bitrix\Main\IO\Directory(
            $this->createPath(Application::getDocumentRoot(), BX_ROOT, 'components', 'fi1a')
        );
        $unlink = true;
        foreach ($componentDirectory->getChildren() as $entry) {
            if ($entry->isFile() || $entry->isDirectory()) {
                $unlink = false;

                break;
            }
        }
        unset($entry);

        if ($unlink) {
            try {
                $componentDirectory->delete();
            } catch (FileDeleteException $exception) {
                $APPLICATION->ResetException();
                $APPLICATION->ThrowException($exception->getMessage());

                return false;
            }
        }

        return true;
    }
}
