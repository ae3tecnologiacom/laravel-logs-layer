<?php

namespace Ae3\LaravelLogsLayer\app\Observers;

use Ae3\LaravelLogsLayer\app\Containers\LogDataContainer;
use Ae3\LaravelLogsLayer\app\Events\GuzzleEventCaptured;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;

class LogCaptureObserver
{
    /**
     * @return void
     */
    public static function registerListeners()
    {
        Event::listen(QueryExecuted::class, [static::class, 'captureExecutedQueries']);
        Event::listen(GuzzleEventCaptured::class, [static::class, 'captureGuzzleEvents']);
    }

    /**
     * @param QueryExecuted $query
     * @return void
     */
    public static function captureExecutedQueries(QueryExecuted $query)
    {
        $logDataContainer = app(LogDataContainer::class);

        $logDataContainer->addCapturedQuery([
            'query' => $query->sql,
            'bindings' => $query->bindings,
        ]);
    }

    public static function captureGuzzleEvents(GuzzleEventCaptured $event)
    {
        $logDataContainer = app(LogDataContainer::class);

        $logDataContainer->addCapturedHttpClientEvent([
            'request' => [
                'method' => $event->request->getMethod(),
                'uri' => $event->request->getUri(),
                'headers' => $event->request->getHeaders(),
            ],
            'options' => $event->options,
        ]);
    }
}