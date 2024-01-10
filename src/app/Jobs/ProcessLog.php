<?php

namespace Ae3\LaravelLogsLayer\app\Jobs;

use Ae3\LaravelLogsLayer\app\Exceptions\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $channel;
    /**
     * @var string
     */
    protected string $level;
    /**
     * @var string
     */
    protected string $message;
    /**
     * @var array
     */
    protected array $data;

    /**
     * @param string $channel
     * @param string $level
     * @param string $message
     * @param array $data
     */
    public function __construct(
        string $channel,
        string $level,
        string $message,
        array $data
    )
    {
        $this->level = $level;
        $this->message = $message;
        $this->data = $data;
        $this->channel = $channel;
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(config('laravel-logs-layer.queue.retry_until_in_minutes', 60));
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return array_map('intval', config('laravel-logs-layer.queue.backoff', ['5','10','20','40']));
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $level = $this->level;
        Log::channel($this->channel)->$level($this->message, $this->data);
    }
}