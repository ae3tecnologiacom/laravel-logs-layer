<?php

namespace Ae3\LaravelLogsLayer\app\Services;

class DailyLogService extends AbstractLogService
{

    /**
     * @inheritDoc
     */
    protected function getLogChannel(): string
    {
        return 'daily';
    }

    /**
     * @inheritDoc
     */
    protected function log(string $level, string $message, array $data): void
    {
        $log_message = json_encode($data);
        $log_filename = 'laravel-' . now()->format('Y-m-d') . '.log';
        $log_file_path = storage_path('logs/' . $log_filename);

        // Abra o arquivo para escrita e adicione a mensagem de log
        file_put_contents($log_file_path, $log_message . PHP_EOL, FILE_APPEND);
    }

}