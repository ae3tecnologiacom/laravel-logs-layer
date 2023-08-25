<?php

namespace Ae3\LaravelLogsLayer\app\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class LoggedExceptionDTO extends DataTransferObject
{
    /**
     * @var string
     */
    public string $code;
    /**
     * @var string
     */
    public string $message;
    /**
     * @var string
     */
    public string $level;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self([
            'code' => $data['code'],
            'message' => $data['message'],
            'level' => $data['level'],
        ]);
    }
}