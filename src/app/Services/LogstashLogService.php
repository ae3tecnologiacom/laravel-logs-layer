<?php

namespace Ae3\LaravelLogsLayer\app\Services;

class LogstashLogService extends AbstractLogService
{
    /**
     * @inheritDoc
     */
    protected function getLogChannel(): string
    {
        return 'logstash';
    }
}