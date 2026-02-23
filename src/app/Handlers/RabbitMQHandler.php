<?php

namespace Ae3\LaravelLogsLayer\app\Handlers;

use Exception;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQHandler extends AbstractProcessingHandler
{
    /**
     * @var AMQPStreamConnection
     */
    private AMQPStreamConnection $connection;
    /**
     * @var AbstractChannel|AMQPChannel
     */
    private $channel;
    /**
     * @var mixed|string
     */
    private $exchange;
    /**
     * @var mixed|string
     */
    private $routingKey;

    /**
     * @throws Exception
     */
    public function __construct($exchange = 'logs', $routingKey = 'log', $level = 400, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->connection = new AMQPStreamConnection(
            config('logging.channels.rabbitmq.host'),
            config('logging.channels.rabbitmq.port'),
            config('logging.channels.rabbitmq.username'),
            config('logging.channels.rabbitmq.password'),
            config('logging.channels.rabbitmq.vhost', '/')
        );
        $this->channel = $this->connection->channel();

        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->channel->exchange_declare($exchange, 'direct', false, true, false);

        $queueName = config('logging.channels.rabbitmq.queue', 'logstash_queue');
        $this->channel->queue_declare($queueName, false, true, false, false);
        $this->channel->queue_bind($queueName, $this->exchange, $this->routingKey);
    }

    /**
     * @param mixed $record
     * @return void
     */
    public function write($record): void
    {
        if (is_array($record)) {
            // Implementação para Monolog 1.x
            $this->recordHandler($record);
        }elseif (class_exists(LogRecord::class) && $record instanceof LogRecord) {
            // Implementação para Monolog 2.x
            $arrayRecord = $record->toArray();
            $this->recordHandler($arrayRecord);
        }
    }

    /**
     * @param array $record
     * @return void
     */
    protected function recordHandler(array $record)
    {
        // Garante que 'level' seja string (nome do nível) e não número
        // Isso mantém compatibilidade com o Logstash que espera string
        if (isset($record['level_name'])) {
            $record['level'] = $record['level_name'];
        }
        
        $data = json_encode($record);
        $msg = new AMQPMessage($data, [
            'delivery_mode' => 2
        ]);

        $this->channel->basic_publish($msg, $this->exchange, $this->routingKey);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}