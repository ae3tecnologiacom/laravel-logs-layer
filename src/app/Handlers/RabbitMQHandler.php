<?php

namespace Ae3\LaravelLogsLayer\app\Handlers;

use Exception;
use Monolog\Handler\AbstractProcessingHandler;
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
    public function __construct($exchange = 'logs', $routingKey = 'log', $level = \Monolog\Level::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->connection = new AMQPStreamConnection(
            config('logging.channels.rabbitmq.host'),
            config('logging.channels.rabbitmq.port'),
            config('logging.channels.rabbitmq.username'),
            config('logging.channels.rabbitmq.password')
        );
        $this->channel = $this->connection->channel();

        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->channel->exchange_declare($exchange, 'direct', false, true, false);
    }

    /**
     * @param array $record
     * @return void
     */
    public function write(array $record): void
    {
        $data = json_encode($record);
        $msg = new AMQPMessage($data, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
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