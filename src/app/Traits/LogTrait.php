<?php

namespace Ae3\LaravelLogsLayer\app\Traits;

use Ae3\LaravelLogsLayer\app\DataTransferObjects\ExceptionContextDTO;
use Ae3\LaravelLogsLayer\app\DataTransferObjects\LoggedExceptionDTO;
use Ae3\LaravelLogsLayer\app\Enums\LogsLevelsEnum;
use Ae3\LaravelLogsLayer\app\Exceptions\InvalidArgumentException;
use Ae3\LaravelLogsLayer\app\Services\DailyLogService;
use Ae3\LaravelLogsLayer\app\Services\DiscordLogService;
use Ae3\LaravelLogsLayer\app\Services\EmailLogService;
use Ae3\LaravelLogsLayer\app\Services\LogstashLogService;
use Hashids\Hashids;
use Illuminate\Support\Str;
use Throwable;

trait LogTrait
{
    /**
     * @var array
     */
    private array $logServices = [];

    /**
     * @return array
     */
    public function getLogServices(): array
    {
        return $this->logServices;
    }

    /**
     * @return void
     */
    public function initializeLogServices(): void
    {
        $defaultChannel = config('logging.default');
        $stackChannels = config('logging.channels.stack.channels');

        if ($defaultChannel === 'logstash' || in_array('logstash', $stackChannels, true)) {
            $this->logServices[] = app(LogstashLogService::class);
        }

        if ($defaultChannel === 'daily' || in_array('daily', $stackChannels, true)) {
            $this->logServices[] = app(DailyLogService::class);
        }

        if ($defaultChannel === 'discord' || in_array('discord', $stackChannels, true)) {
            $this->logServices[] = app(DiscordLogService::class);
        }

        if ($defaultChannel === 'email' || in_array('email', $stackChannels, true)) {
            $this->logServices[] = app(EmailLogService::class);
        }
    }

    /**
     * @param string $method
     * @param string $message
     * @param array $customData
     * @return void
     */
    private function logWithServices(string $method, string $message, array $customData = []): void
    {
        $this->initializeLogServices();

        foreach ($this->logServices as $logService) {
            $logService->$method($message, $customData);
        }
    }

    /**
     * @param string $message
     * @param array $customData
     * @return void
     */
    public function logInfo(string $message, array $customData = []): void
    {
        $this->logWithServices(LogsLevelsEnum::INFO, $message, $customData);
    }

    /**
     * @param string $message
     * @param array $customData
     * @return void
     */
    public function logNotice(string $message, array $customData = []): void
    {
        $this->logWithServices(LogsLevelsEnum::NOTICE, $message, $customData);
    }

    /**
     * @param string $message
     * @param array $customData
     * @return void
     */
    public function logWarning(string $message, array $customData = []): void
    {
        $this->logWithServices(LogsLevelsEnum::WARNING, $message, $customData);
    }

    /**
     * @param string $message
     * @param array $customData
     * @return void
     */
    public function logDebug(string $message, array $customData = []): void
    {
        $this->logWithServices(LogsLevelsEnum::DEBUG, $message, $customData);
    }

    /**
     * @param string $message
     * @param array $customData
     * @return void
     */
    public function logAlert(string $message, array $customData = []): void
    {
        $this->logWithServices(LogsLevelsEnum::ALERT, $message, $customData);
    }

    /**
     * @param string $caller
     * @param Throwable $exception
     * @param string $log_level
     * @param array $customData
     * @return LoggedExceptionDTO
     * @throws InvalidArgumentException
     */
    public function logException(string $caller, Throwable $exception, string $log_level = LogsLevelsEnum::ERROR, array $customData = []): LoggedExceptionDTO
    {
        if (!in_array($log_level, LogsLevelsEnum::allErrorLevels(), true)) {
            throw new InvalidArgumentException('Invalid log level specified');
        }

        $errorCode = $this->getRandomErrorCode();

        $this->initializeLogServices();

        foreach ($this->logServices as $logService) {
            $logService->$log_level($caller, $exception, ExceptionContextDTO::fromArray([
                'code' => $errorCode,
                'root_namespace' => $this->getRootNamespaceFromCaller($caller),
                'custom_data' => $customData,
                'current_url' => request()->fullUrl(),
                'current_user' => auth()->user(),
                'tags' => [],
            ]));
        }

        return LoggedExceptionDTO::fromArray([
            'code' => $errorCode,
            'message' => __("Error code: :code", ['code' => $errorCode]),
            'level' => $log_level,
        ]);
    }

    /**
     * @return string
     */
    private function getRandomErrorCode(): string
    {
        $microtime = explode(' ', microtime());
        $milliseconds = ((int)$microtime[1]) * 1000 + (int)round($microtime[0] * 1000);
        return (new Hashids())->encode($milliseconds);
    }

    /**
     * @param string $caller
     * @return string
     */
    private function getRootNamespaceFromCaller(string $caller): string
    {
        return Str::before($caller, '\\');
    }
}