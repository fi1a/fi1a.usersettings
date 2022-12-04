<?php

declare(strict_types=1);

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\EventResult as OrmEventResult;
use Fi1a\Collection\DataType\ArrayObject;
use Fi1a\UserSettings\Internals\TabsTable;
use Fi1a\UserSettings\Internals\TransactionState;

use function htmlspecialcharsbx;

/**
 * Класс вкладки в пользовательских настройках
 */
class Tab extends ArrayObject implements TabInterface
{
    use TransactionState;

    /**
     * @var array
     */
    private static $languages = null;

    /**
     * @var \Bitrix\Main\DB\Connection
     */
    protected $connection = null;

    /**
     * @inheritDoc
     */
    public static function create(array $input = []): TabInterface
    {
        return new static($input);
    }

    /**
     * Конструктор
     *
     * @param string[] $input
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function __construct(array $input = [])
    {
        // Языки
        if (!self::$languages) {
            $languagesIterator = LanguageTable::getList([
                'order' => [
                    'SORT' => 'ASC',
                ],
                'select' => ['LID',],
            ]);

            while ($language = $languagesIterator->fetch()) {
                self::$languages[] = $language['LID'];
            }
            unset($language);
        }

        // Добавляем не достающие языки
        if (isset($input['LOCALIZATION'])) {
            foreach (self::$languages as $language) {
                if (isset($input['LOCALIZATION'][$language])) {
                    continue;
                }

                $input['LOCALIZATION'][$language] = [
                    'L_NAME' => '',
                    'L_TITLE' => '',
                ];
            }
            unset($language);
        }

        $this->connection = Application::getConnection();

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
        $fields = $this->getArrayCopy();
        unset($fields['ID']);

        try {
            $result = new AddResult();

            $event = new Event('fi1a.usersettings', 'OnBeforeTabAdd', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_TAB_ON_BEFORE_ADD_ERROR'))
                    );

                    return $result;
                }
                $parameters = $eventResult instanceof OrmEventResult
                    ? $eventResult->getModified()
                    : $eventResult->getParameters()['fields'];
                $fields = array_replace_recursive($fields, $parameters);
            }
            unset($eventResult);

            $this->connection->startTransaction();
            $this->inTransaction();

            $result = TabsTable::add($fields);
        } catch (\Throwable $exception) {
            $result = new AddResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();
            $this->notInTransaction();

            $fields['ID'] = $result->getId();
            $event = new Event('fi1a.usersettings', 'OnAfterTabAdd', ['fields' => $fields]);
            $event->send();

            $this->exchangeArray(array_replace_recursive($this->getArrayCopy(), $fields));

            return $result;
        }

        if ($this->isInTransaction()) {
            $this->connection->rollbackTransaction();
            $this->notInTransaction();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function update(): UpdateResult
    {
        $fields = $this->getArrayCopy();
        $id = $fields['ID'];

        if (!$id) {
            $result = new UpdateResult();
            $result->addError(new Error(Loc::getMessage('FUS_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        try {
            $result = new UpdateResult();

            $event = new Event('fi1a.usersettings', 'OnBeforeTabUpdate', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_TAB_ON_BEFORE_UPDATE_ERROR'))
                    );

                    return $result;
                }
                $parameters = $eventResult instanceof OrmEventResult
                    ? $eventResult->getModified()
                    : $eventResult->getParameters()['fields'];
                $fields = array_replace_recursive($fields, $parameters);
            }
            unset($eventResult);

            $this->connection->startTransaction();
            $this->inTransaction();

            $result = TabsTable::update($id, $fields);
        } catch (\Throwable $exception) {
            $result = new UpdateResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();
            $this->notInTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterTabUpdate', ['fields' => $fields]);
            $event->send();

            $this->exchangeArray(array_replace_recursive($this->getArrayCopy(), $fields));

            return $result;
        }

        if ($this->isInTransaction()) {
            $this->connection->rollbackTransaction();
            $this->notInTransaction();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete(): DeleteResult
    {
        $fields = $this->getArrayCopy();
        $id = (int) $fields['ID'];

        if (!$id) {
            $result = new DeleteResult();
            $result->addError(new Error(Loc::getMessage('FUS_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        try {
            $result = new DeleteResult();

            $event = new Event('fi1a.usersettings', 'OnBeforeTabDelete', ['fields' => $fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                /**
                 * @var OrmEventResult $eventResult
                 */
                if ($eventResult->getType() === EventResult::ERROR) {
                    $result->addErrors(
                        $eventResult instanceof OrmEventResult
                            ? $eventResult->getErrors()
                            : new Error(Loc::getMessage('FUS_TAB_ON_BEFORE_DELETE_ERROR'))
                    );

                    return $result;
                }
            }
            unset($eventResult);

            $this->connection->startTransaction();
            $this->inTransaction();

            $result = TabsTable::delete($id);

            if ($result->isSuccess()) {
                $fieldCollection = FieldMapper::getByTabId($id);
                foreach ($fieldCollection as $field) {
                    /**
                     * @var FieldInterface $field
                     */
                    $fieldDeleteResult = $field->delete();

                    if (!$fieldDeleteResult->isSuccess()) {
                        $result->addErrors($fieldDeleteResult->getErrors());

                        break;
                    }
                }
                unset($field);
            }
        } catch (\Throwable $exception) {
            $result = new DeleteResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();
            $this->notInTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterTabDelete', ['fields' => $fields]);
            $event->send();

            return $result;
        }

        if ($this->isInTransaction()) {
            $this->connection->rollbackTransaction();
            $this->notInTransaction();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getName(string $langId = LANGUAGE_ID): string
    {
        $name = $this['CODE'];
        if ($this['LOCALIZATION'][$langId]['L_NAME']) {
            $name = $this['LOCALIZATION'][$langId]['L_NAME'];
        }

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(string $langId = LANGUAGE_ID): string
    {
        $title = '';
        if ($this['LOCALIZATION'][$langId]['L_TITLE']) {
            $title = htmlspecialcharsbx($this['LOCALIZATION'][$langId]['L_TITLE']);
        }

        return $title;
    }
}
