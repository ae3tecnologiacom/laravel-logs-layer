<?php

namespace Ae3\LaravelLogsLayer\app\Loggers;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Ae3\LaravelLogsLayer\app\Handlers\DiscordHandler;
use Ae3\LaravelLogsLayer\app\Loggers\Contracts\LoggerContract;
use Illuminate\Support\Facades\App;
use Monolog\Logger;

class DiscordLogger extends AbstractLogger
{
    /**
     * @param array $config
     * @return void
     * @throws MissingConfigurationException
     */
    public function validateConfig(array $config): void
    {
        $requiredKeys = ['webhook', 'environments'];

        foreach (array_merge($requiredKeys, $this->requiredKeys) as $key) {
            if (!array_key_exists($key, $config)) {
                throw new MissingConfigurationException("Missing configuration key: $key in email channel");
            }
        }
    }

    /**
     * @param array $config
     * @return Logger
     */
    public function createLogger(array $config): Logger
    {
        $log = new Logger('discord');
        $log->pushHandler(new DiscordHandler($config['webhook'], Logger::DEBUG, $config['bubble'] ?? true));

        return $log;
    }

}