<?php

namespace Ae3\LaravelLogsLayer\app\Formatters;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class LogstashTcpFormatter implements FormatterInterface
{
    /**
     * Formata o registro de log para o formato esperado pelo pipeline do Logstash
     *
     * Formato de saída:
     * {
     *   "level": "ERROR",
     *   "message": "texto da mensagem",
     *   "context": {
     *     "custom_data": "...",
     *     "current_url": "...",
     *     "caller": "...",
     *     ...
     *   }
     * }
     */
    public function format(LogRecord $record): string
    {
        $data = [
            'level' => $record->level->getName(),
            'message' => $record->message,
            'context' => $record->context,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    }

    /**
     * Formata múltiplos registros
     *
     * @param array $records
     * @return string
     */
    public function formatBatch(array $records): string
    {
        $formatted = [];
        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }
        return implode("", $formatted);
    }
}
