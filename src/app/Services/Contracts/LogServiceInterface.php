<?php

namespace Ae3\LaravelLogsLayer\app\Services\Contracts;

use Ae3\LaravelLogsLayer\app\DataTransferObjects\ExceptionContextDTO;
use Throwable;

interface LogServiceInterface
{
    /**
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function emergency(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void;

    /**
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function alert(string $message, array $messageContext = []): void;

    /**
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function critical(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void;

    /**
     * @param string $caller
     * @param Throwable $exception
     * @param ExceptionContextDTO $contextDto
     * @return void
     */
    public function error(string $caller, Throwable $exception, ExceptionContextDTO $contextDto): void;

    /**
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function warning(string $message, array $messageContext = []): void;

    /**
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function notice(string $message, array $messageContext = []): void;

    /**
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function info(string $message, array $messageContext = []): void;

    /**
     * @param string $message
     * @param array $messageContext
     * @return void
     */
    public function debug(string $message, array $messageContext = []): void;
}