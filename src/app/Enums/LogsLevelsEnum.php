<?php

namespace Ae3\LaravelLogsLayer\app\Enums;

class LogsLevelsEnum
{
    const EMERGENCY = "emergency";
    const CRITICAL = "critical";
    const ERROR = "error";
    const INFO = "info";
    const NOTICE = "notice";
    const WARNING = "warning";
    const DEBUG = "debug";
    const ALERT = "alert";

    /**
     * @return array
     */
    public static function allErrorLevels(): array
    {
        return [
            self::EMERGENCY,
            self::CRITICAL,
            self::ERROR,
        ];
    }
}