<?php

namespace Ae3\LaravelLogsLayer\app\Handlers;

use DateTimeInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Monolog\LogRecord;

class DiscordHandler extends AbstractProcessingHandler
{
    /**
     * @var string
     */
    private string $webhook;
    /**
     * @var Client
     */
    private Client $client;
    /**
     * @var int
     */
    private int $rateLimitRemaining = 0;
    /**
     * @var ?int
     */
    private ?int $rateLimitReset = null;

    /**
     * @param string $webhook
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(string $webhook, \Monolog\Level $level = \Monolog\Level::ERROR, bool $bubble = true)
    {
        $this->webhook = $webhook;
        $this->client = new Client();

        parent::__construct($level, $bubble);
    }

    /**
     * @param LogRecord $record
     * @return void
     * @throws GuzzleException
     */
    protected function write(LogRecord $record): void
    {
        if ($this->rateLimitRemaining === 0 && $this->rateLimitReset !== null) {
            $this->waitUntil($this->rateLimitReset);
        }

        try {
            $response = $this->send($record->toArray());
        } catch (ClientException $exception) {
            $response = $exception->getResponse();

            if ($response->getStatusCode() !== Response::HTTP_TOO_MANY_REQUESTS) {
                throw $exception;
            }

            $retryAfter = $response->getHeaderLine('Retry-After');
            $this->wait((int)$retryAfter);

            $this->send($record->toArray());
        }

        $this->rateLimitRemaining = (int)$response->getHeaderLine('X-RateLimit-Remaining');
        $this->rateLimitReset = (int)$response->getHeaderLine('X-RateLimit-Reset');
    }

    /**
     * @param array $record
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function send(array $record): ResponseInterface
    {
        return $this->client->request('POST', $this->webhook, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $this->formatMessage($record),
        ]);
    }

    /**
     * @param array $record
     * @return array[]
     */
    private function formatMessage(array $record): array
    {
        return [
            'embeds' => $this->formatEmbeds($record),
        ];
    }

    /**
     * @param array $record
     * @return array[]
     */
    private function formatEmbeds(array $record): array
    {
        $fields = [];

        foreach ($record['context'] as $key => $value) {
            $value = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string)$value;

            $fields[] = [
                'name' => $key,
                'value' => $value,
                'inline' => true,
            ];
        }

        return [
            [
                'title' => $record['message'],
                'timestamp' => $record['datetime']->format(DateTimeInterface::ATOM),
                'fields' => $fields,
                'footer' => [
                    'text' => 'level.' . $record['level_name'],
                ],
            ]
        ];
    }

    /**
     * @param int $microseconds
     * @return void
     */
    private function wait(int $microseconds): void
    {
        usleep($microseconds);
    }

    /**
     * @param int $timestamp
     * @return void
     */
    private function waitUntil(int $timestamp): void
    {
        time_sleep_until($timestamp);
    }
}
