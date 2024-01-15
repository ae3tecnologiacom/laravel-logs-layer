<?php

namespace Ae3\LaravelLogsLayer\app\Services;

use Ae3\LaravelLogsLayer\app\DataTransferObjects\ExceptionContextDTO;
use Throwable;

class RabbitMQLogService extends AbstractLogService
{
    /**
     * @inheritDoc
     */
    protected function getLogChannel(): string
    {
        return 'rabbitmq';
    }

    /**
     * @inheritDoc
     */
    protected function buildLogData(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): array
    {
        return [
            'caller' => $caller,
            'status_code' => $exception->getCode(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'error_code' => $contextDto->code,
            'custom_data' => $this->asPrettyJson($contextDto->custom_data),
            'tags' => $contextDto->tags,
            'exception' => get_class($exception),
            'current_url' => $contextDto->current_url,
            'current_user' => $this->asPrettyJson($contextDto->current_user),
            'classes' => $this->asPrettyJson($this->getContextClasses($exception->getTrace(), $contextDto->root_namespace)),
        ];
    }
}