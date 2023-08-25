<?php

namespace Ae3\LaravelLogsLayer\app\Services;

class EmailLogService extends AbstractLogService
{
    /**
     * @inheritDoc
     */
    protected function getLogChannel(): string
    {
        return 'email';
    }
}