<?php

namespace Ae3\LaravelLogsLayer\app\Middlewares;

use Ae3\LaravelLogsLayer\app\Events\GuzzleEventCaptured;
use Closure;
use Psr\Http\Message\RequestInterface;

class GuzzleLoggingMiddleware
{
    /**
     * @param callable $handler
     * @return Closure
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            event(new GuzzleEventCaptured($request, $options));
            return $handler($request, $options);
        };
    }
}