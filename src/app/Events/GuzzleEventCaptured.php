<?php

namespace Ae3\LaravelLogsLayer\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Psr\Http\Message\RequestInterface;

class GuzzleEventCaptured
{
    use Dispatchable;

    /**
     * @var RequestInterface
     */
    public RequestInterface $request;
    /**
     * @var array
     */
    public array $options;

    /**
     * @param RequestInterface $request
     * @param array $options
     */
    public function __construct(RequestInterface $request, array $options)
    {
        $this->request = $request;
        $this->options = $options;
    }
}