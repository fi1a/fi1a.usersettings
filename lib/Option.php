<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\EventResult as OrmEventResult;
use Bitrix\Main\Result;
use Fi1a\UserSettings\Exceptions\OptionGetValueException;
use Fi1a\UserSettings\Helpers\ModuleRegistry;

use function htmlspecialcharsbx;

/**
 * Класс реализующий работу со значениями пользовательских настроек
 */
class Option implements IOption
{
    /**
     * @var IOption
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $userFields = null;

    /**
     * @var \Bitrix\Main\Data\ManagedCache
     */
    private $cache = null;

    /**
     * @inheritDoc
     */
    public static function getInstance(): IOption
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Конструктор
     *
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct()
    {
        $this->cache = Application::getInstance()->getManagedCache();
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        if (is_null($this->userFields)) {
            $this->userFields = $this->getUserFields();
        }

        $values = [];
        foreach ($this->userFields as $userField) {
            $values[$userField['FIELD_NAME']] = $userField['VALUE'];
        }
        unset($userField);

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = false)
    {
        if (is_null($this->userFields)) {
            $this->userFields = $this->getUserFields();
        }

        $fields = ['key' => $key, 'value' => $this->userFields[$key]['VALUE'], 'default' => $default];

        $event = new Event('fi1a.usersettings', 'OnOptionGet', $fields);
        $event->send();
        foreach ($event->getResults() as $eventResult) {
            /**
             * @var OrmEventResult $eventResult
             */
            if ($eventResult->getType() === EventResult::ERROR) {
                $errorMessage = Loc::getMessage('FUS_ON_BEFORE_OPTION_GET_ERROR');
                if ($eventResult instanceof OrmEventResult) {
                    $errorMessage = '';
                    foreach ($eventResult->getErrors() as $error) {
                        $errorMessage .= ' ' . $error->getMessage();
                    }
                }

                throw new OptionGetValueException($errorMessage);
            }
            $parameters = $eventResult instanceof OrmEventResult
                ? $eventResult->getModified()
                : $eventResult->getParameters();
            $fields = array_replace_recursive($fields, $parameters);
        }
        unset($eventResult);

        if (is_null($fields['value']) || $fields['value'] === false) {
            return $fields['default'];
        }

        return $fields['value'];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): Result
    {
        $result = new Result();

        if (is_null($this->userFields)) {
            $this->userFields = $this->getUserFields();
        }

        if (!isset($this->userFields[$key])) {
            $result->addError(
                new Error(
                    Loc::getMessage('FUS_FIELD_NOT_EXIST', ['#CODE#' => htmlspecialcharsbx($key)])
                )
            );

            return $result;
        }

        $userTypeManager = UserTypeManager::getInstance();

        $fields = [$key => $value];

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeOptionSet', $fields);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_ON_BEFORE_OPTION_SET_ERROR'))
                    );

                    return $result;
                }
                $parameters = $eventResult instanceof OrmEventResult
                    ? $eventResult->getModified()
                    : $eventResult->getParameters();
                $fields = array_replace_recursive($fields, $parameters);
            }
            unset($eventResult);

            if (!$userTypeManager->CheckFields(static::ENTITY_ID, static::ID, $fields)) {
                $result->addError(
                    new Error(ModuleRegistry::getApplication()->GetException()->GetString())
                );

                return $result;
            }

            $userTypeManager->Update(static::ENTITY_ID, static::ID, $fields);

            $this->userFields[$key]['VALUE'] = $fields[$key];

            $this->clearCache();
        } catch (\Throwable $exception) {
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $event = new Event('fi1a.usersettings', 'OnAfterOptionSet', $fields);
            $event->send();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function clearCache(): bool
    {
        $this->userFields = null;
        $userTypeManager = UserTypeManager::getInstance();
        $userTypeManager->CleanCache();
        $this->cache->clean(static::CACHE_ID);

        return true;
    }

    /**
     * Возвращает пользовательские поля
     *
     * @return string[]
     *
     * @throws \Bitrix\Main\SystemException
     */
    protected function getUserFields(): array
    {
        if ($this->cache->read(60 * 60 * 3, static::CACHE_ID)) {
            // @codeCoverageIgnoreStart
            $fields = $this->cache->get(static::CACHE_ID);
            // @codeCoverageIgnoreEnd
        } else {
            $fields = [];

            $userTypeManager = UserTypeManager::getInstance();

            $userFields = $userTypeManager->GetUserFields(static::ENTITY_ID, static::ID);

            foreach ($userFields as $userField) {
                $fields[$userField['FIELD_NAME']] = $userField;
            }

            $this->cache->set(static::CACHE_ID, $fields);
        }

        return $fields;
    }
}
