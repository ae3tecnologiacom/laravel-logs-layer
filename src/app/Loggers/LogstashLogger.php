<?php

namespace Ae3\LaravelLogsLayer\app\Loggers;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Level;
use Monolog\Logger;

class LogstashLogger extends AbstractLogger
{
    /**
     * @param array $config
     * @return void
     * @throws MissingConfigurationException
     */
    public function validateConfig(array $config): void
    {
        $requiredKeys = ['host', 'port'];

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
        $handler = new SocketHandler(
            "tcp://{$config['host']}:{$config['port']}",
            $config['level'] ?? Level::Debug,
            $config['bubble'] ?? true
        );
        $handler->setFormatter(new LogstashFormatter(config('app.name')));
        return new Logger('logstash.main', [$handler]);
    }
}