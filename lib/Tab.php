<?php

namespace Fi1a\UserSettings;

use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Data\DeleteResult;
use Fi1a\UserSettings\Collection\ArrayObject;
use Fi1a\UserSettings\Internals\TabsTable;

Loc::loadMessages(__FILE__);

/**
 * Класс вкладки в пользовательских настройках
 */
class Tab extends ArrayObject implements ITab
{

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
    public static function create(array $input = []): ITab
    {
        return new static($input);
    }

    /**
     * Конструктор
     *
     * @param array $input
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(array $input = [])
    {
        // Языки
        if (!static::$languages) {
            $languagesIterator = LanguageTable::getList([
                'order' => [
                    'SORT' => 'ASC',
                ],
                'select' => ['LID',],
            ]);

            while ($language = $languagesIterator->fetch()) {
                static::$languages[] = $language['LID'];
            }
            unset($language);
        }

        // Добавляем не достающие языки
        if (isset($input['LOCALIZATION'])) {
            foreach (static::$languages as $language) {
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

        parent::__construct($input, 0, 'ArrayIterator');
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        return (int)$this['ID'] > 0 ? $this->update() : $this->add();
    }

    /**
     * @inheritDoc
     */
    public function add(): AddResult
    {
        $this->connection->startTransaction();

        $fields = $this->getArrayCopy();
        unset($fields['ID']);

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeTabAdd', [$fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() == EventResult::ERROR) {
                    continue;
                }

                $fields = array_merge($fields, $eventResult->getParameters());
            }
            unset($eventResult);

            $result = TabsTable::add($fields);
        } catch (\Exception $exception) {
            $result = new AddResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $fields['ID'] = $result->getId();
            $event = new Event('fi1a.usersettings', 'OnAfterTabAdd', [$fields]);
            $event->send();

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

        if (!$id) {
            $result = new UpdateResult();
            $result->addError(new Error(Loc::getMessage('FUS_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        $this->connection->startTransaction();

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeTabUpdate', [$fields]);
            $event->send();
            foreach ($event->getResults() as $eventResult) {
                if ($eventResult->getType() == EventResult::ERROR) {
                    continue;
                }

                $fields = array_merge($fields, $eventResult->getParameters());
            }
            unset($eventResult);

            $result = TabsTable::update($id, $fields);
        } catch (\Exception $exception) {
            $result = new UpdateResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterTabUpdate', [$fields]);
            $event->send();

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
        $id = $fields['ID'];

        $this->connection->startTransaction();

        if (!$id) {
            $result = new DeleteResult();
            $result->addError(new Error(Loc::getMessage('FUS_PRIMARY_VALUE_NOT_EXISTS')));

            return $result;
        }

        try {
            $event = new Event('fi1a.usersettings', 'OnBeforeTabDelete', [$fields]);
            $event->send();

            $result = TabsTable::delete($id);

            if ($result->isSuccess()) {
                $fieldCollection = FieldMapper::getByTabId($id);
                foreach ($fieldCollection as $field) {
                    /**
                     * @var IField $field
                     */
                    $fieldDeleteResult = $field->delete();

                    if (!$fieldDeleteResult->isSuccess()) {
                        $result->addErrors($fieldDeleteResult->getErrors());

                        break;
                    }
                }
                unset($field);
            }
        } catch (\Exception $exception) {
            $result = new DeleteResult();
            $result->addError(new Error($exception->getMessage()));
        }

        if ($result->isSuccess()) {
            $this->connection->commitTransaction();

            $event = new Event('fi1a.usersettings', 'OnAfterTabDelete', [$fields]);
            $event->send();

            return $result;
        }

        $this->connection->rollbackTransaction();

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
            $title = \htmlspecialcharsbx($this['LOCALIZATION'][$langId]['L_TITLE']);
        }

        return $title;
    }
}
