<?php

declare(strict_types=1);

namespace Fi1a\UserSettings\Internals;

trait TransactionState
{
    /**
     * @var bool
     */
    private $isInTransaction = false;

    /**
     * Текущее состояние транзакции
     */
    protected function isInTransaction(): bool
    {
        return $this->isInTransaction;
    }

    /**
     * Состояние в транзакции
     */
    protected function inTransaction(): void
    {
        $this->isInTransaction = true;
    }

    /**
     * Состояние не в транзакции
     */
    protected function notInTransaction(): void
    {
        $this->isInTransaction = true;
    }
}
