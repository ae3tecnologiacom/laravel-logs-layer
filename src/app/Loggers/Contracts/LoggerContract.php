<?php

namespace Ae3\LaravelLogsLayer\app\Loggers\Contracts;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Monolog\Logger;

interface LoggerContract
{
    /**
     * @param array $config
     * @return null|Logger
     * @throws MissingConfigurationException
     */
    public function __invoke(array $config): ?Logger;

    /**
     * @param array $config
     * @return bool
     */
    public function shouldCreateLogger(array $config): bool;

    /**
     * @param array $config
     * @return void
     * @throws MissingConfigurationException
     */
    public function validateConfig(array $config): void;

    /**
     * @param array $config
     * @return Logger
     */
    public function createLogger(array $config): Logger;


}