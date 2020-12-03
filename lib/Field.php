<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\EventResult as OrmEventResult;
use CUserTypeEntity;
use Fi1a\Collection\DataType\ArrayObject;
use Fi1a\UserSettings\Helpers\ModuleRegistry;
use Fi1a\UserSettings\Internals\FieldsTable;

/**
 * Поле пользовательских настроек
 */
class Field extends ArrayObject implements IField
{
    /**
     * @var \Bitrix\Main\DB\Connection
     */
    protected $connection = null;

    /**
     * @var Option
     */
    protected $options = null;

    /**
     * @inheritDoc
     */
    public static function create(array $input = []): IField
    {
        return new static($input);
    }

    /**
     * Конструктор
     *
     * @param mixed[]|null $input
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function __construct(?array $input = [])
    {
        $this->connection = Application::getConnection();
        $this->options = Option::getInstance();

        parent::__construct($input);
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        return (int) $this['ID'] > 0 ? $this->update() : $this->add();
    }

    /**
     * @inheritDoc
     */
    public function add(): AddResult
    {
        $this->connection->startTransaction();

        $fields = $this->getArrayCopy();
        unset($fields['ID']);
        unset($fields['UF_ID']);
        unset($fields['UF']['ID']);

        $fields['UF']['ENTITY_ID'] = IOption::ENTITY_ID;

        try {
            $result = new AddResult();

            $event = new Event('fi1a.usersettings', 'OnBeforeFieldAdd', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                        ? $eventResult->getErrors()
                        : new Error(Loc::getMessage('FUS_ON_BEFORE_ADD_ERROR'))
                    );

                    return $result;
                }
                $parameters = $eventResult instanceof OrmEventResult
                    ? $eventResult->getModified()
                    : $eventResult->getParameters()['fields'];
                $fields = array_replace_recursive($fields, $parameters);
            }
            unset($eventResult);

            $userTypeEntity  = new CUserTypeEntity();
            $userTypeId = $userTypeEntity->Add($fields['UF']);

            if (!$userTypeId) {
                $result->addError(
                    new EntityError(ModuleRegistry::getApplication()->GetException()->GetString())
                );
            }

            if ($result->isSuccess()) {
                $fields['UF_ID'] = $userTypeId;
                $result = FieldsTable::add($fields);

                if (!$result->isSuccess()) {
                    $userTypeEntity->Delete($userTypeId);
                } else {
                    $result->setData([
                        'UF_ID' => $userTypeId,
                    ]);
                }
            }
        } catch (\Throwable $exception) {
            $result = new AddResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $fields['ID'] = $result->getId();
            $event = new Event('fi1a.usersettings', 'OnAfterFieldAdd', ['fields' => $fields]);
            $event->send();

            $this->exchangeArray(array_replace_recursive($this->getArrayCopy(), $fields));

            $this->options->clearCache();

            return $result;
        }

        $this->connection->rollbackTransaction();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function update(): UpdateResult
    {
        $fields = $this->getArrayCopy();
        $id = $fields['ID'];
        $ufId = $fields['UF_ID'] ? $fields['UF_ID'] : $fields['UF']['ID'];

        if (!$fields['UF']['ID']) {
            $fields['UF']['ID'] = $ufId;
        }

        $result = new UpdateResult();

        $this->connection->startTransaction();

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeFieldUpdate', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_ON_BEFORE_UPDATE_ERROR'))
                    );

                    return $result;
                }
                $parameters = $eventResult instanceof OrmEventResult
                    ? $eventResult->getModified()
                    : $eventResult->getParameters()['fields'];
                $fields = array_replace_recursive($fields, $parameters);
            }
            unset($eventResult);

            if (is_array($fields['UF']) && count($fields['UF']) > 0) {
                $userTypeEntity  = new CUserTypeEntity();

                if (!$userTypeEntity->Update($ufId, $fields['UF'])) {
                    $result->addError(
                        new EntityError(ModuleRegistry::getApplication()->GetException()->GetString())
                    );
                }
            }

            if ($result->isSuccess()) {
                $result = FieldsTable::update($id, $fields);
            }
        } catch (\Throwable $exception) {
            $result = new UpdateResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterFieldUpdate', ['fields' => $fields]);
            $event->send();

            $this->exchangeArray(array_replace_recursive($this->getArrayCopy(), $fields));

            return $result;
        }

        $this->connection->rollbackTransaction();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete(): DeleteResult
    {
        $fields = $this->getArrayCopy();
        $id = (int) $fields['ID'];

        $result = new DeleteResult();

        if (!$id) {
            $result->addError(new Error(Loc::getMessage('FUS_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        $ufId = $fields['UF_ID'] ? (int) $fields['UF_ID'] : (int) $fields['UF']['ID'];
        if (!$ufId) {
            $result->addError(new Error(Loc::getMessage('FUS_UF_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        $this->connection->startTransaction();

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeFieldDelete', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_ON_BEFORE_DELETE_ERROR'))
                    );

                    return $result;
                }
            }
            unset($eventResult);

            $result = FieldsTable::delete($id);

            if ($result->isSuccess()) {
                $userTypeEntity  = new CUserTypeEntity();

                if (!$userTypeEntity->Delete($ufId)) {
                    $result->addError(
                        new Error(ModuleRegistry::getApplication()->GetException()->GetString())
                    );
                }
            }
        } catch (\Throwable $exception) {
            $result = new DeleteResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterFieldDelete', ['fields' => $fields]);
            $event->send();

            $this->options->clearCache();

            return $result;
        }

        $this->connection->rollbackTransaction();

        return $result;
    }
}
