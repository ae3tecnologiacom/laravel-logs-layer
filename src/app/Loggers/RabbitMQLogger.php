<?php

namespace Ae3\LaravelLogsLayer\app\Loggers;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Ae3\LaravelLogsLayer\app\Handlers\RabbitMQHandler;
use Exception;
use Monolog\Level;
use Monolog\Logger;

class RabbitMQLogger extends AbstractLogger
{
    /**
     * @param array $config
     * @return void
     * @throws MissingConfigurationException
     */
    public function validateConfig(array $config): void
    {
        $requiredKeys = ['host', 'port', 'username', 'password', 'vhost'];

        foreach (array_merge($requiredKeys, $this->requiredKeys) as $key) {
            if (!array_key_exists($key, $config)) {
                throw new MissingConfigurationException("Missing configuration key: $key in rabbitmq channel");
            }
        }
    }

    /**
     * @throws Exception
     */
    public function createLogger(array $config): Logger
    {
        $handler = new RabbitMQHandler(
            $config['exchange'] ?? 'logs',
            $config['routing_key'] ?? 'log',
            $config['level'] ?? Level::Debug,
            $config['bubble'] ?? true
        );

        return new Logger('rabbitmq', [$handler]);
    }
}