<?php

namespace Ae3\LaravelLogsLayer\app\Loggers;

use Ae3\LaravelLogsLayer\app\Exceptions\MissingConfigurationException;
use Ae3\LaravelLogsLayer\app\Loggers\Contracts\LoggerContract;
use Illuminate\Support\Facades\App;
use Monolog\Logger;

abstract class AbstractLogger implements LoggerContract
{
    /**
     * @var string[]
     */
    protected array $requiredKeys = ['environments'];

    /**
     * @inheritDoc
     */
    public function __invoke(array $config): ?Logger
    {
        $this->validateConfig($config);

        if (!$this->shouldCreateLogger($config)) {
            return null;
        }

        return $this->createLogger($config);
    }

    /**
     * @inheritDoc
     */
    public function validateConfig(array $config): void
    {
        foreach ($this->requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new MissingConfigurationException("Missing configuration key: $key in email channel");
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function shouldCreateLogger(array $config): bool
    {
        return App::environment(explode(',', $config['environments']));
    }

    /**
     * @inheritDoc
     */
    abstract public function createLogger(array $config): Logger;
}