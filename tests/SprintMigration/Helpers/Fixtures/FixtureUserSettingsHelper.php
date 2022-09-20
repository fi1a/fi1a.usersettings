<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings\SprintMigration\Helpers\Fixtures;

use Fi1a\UserSettings\SprintMigration\Helpers\UserSettingsHelper;
use Throwable;

/**
 * Хелпер модуля sprint.migration
 */
class FixtureUserSettingsHelper extends UserSettingsHelper
{
    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function out($msg, ...$vars)
    {
    }

    /**
     * @param mixed $cond
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outIf($cond, $msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed $val
     * @param mixed $total
     *
     * @return void
     */
    protected function outProgress($msg, $val, $total)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outNotice($msg, ...$vars)
    {
    }

    /**
     * @param mixed $cond
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outNoticeIf($cond, $msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outInfo($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outInfoIf($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outSuccess($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outSuccessIf($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outWarning($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outWarningIf($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outError($msg, ...$vars)
    {
    }

    /**
     * @param mixed $msg
     * @param mixed ...$vars
     *
     * @return void
     */
    protected function outErrorIf($msg, ...$vars)
    {
    }

    /**
     * @param mixed[] $arr1
     * @param mixed[] $arr2
     *
     * @return void
     */
    protected function outDiff(array $arr1, array $arr2)
    {
    }

    /**
     * @param mixed $cond
     * @param mixed[] $arr1
     * @param mixed[] $arr2
     *
     * @return void
     */
    protected function outDiffIf($cond, array $arr1, array $arr2)
    {
    }

    /**
     * @param mixed[] $messages
     *
     * @return void
     */
    protected function outMessages(array $messages = [])
    {
    }

    /**
     * @return void
     */
    protected function outException(Throwable $exception)
    {
    }
}
