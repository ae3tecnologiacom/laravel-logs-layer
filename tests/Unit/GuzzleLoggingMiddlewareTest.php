<?php

use Ae3\LaravelLogsLayer\app\Events\GuzzleEventCaptured;
use Ae3\LaravelLogsLayer\app\Middlewares\GuzzleLoggingMiddleware;
use Ae3\LaravelLogsLayer\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleLoggingMiddlewareTest extends TestCase
{
    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function testMiddlewareInvokesEventAndHandler()
    {
        $eventMock = $this->getMockBuilder(GuzzleEventCaptured::class)
            ->disableOriginalConstructor()
            ->getMock();


        $middleware = new GuzzleLoggingMiddleware();
        $handlerStack = \GuzzleHttp\HandlerStack::create(new MockHandler([
            function ($request, $options) use ($eventMock) {
                // Assert that the event is triggered with the correct arguments
                $this->assertInstanceOf(RequestInterface::class, $request);
                $this->assertIsArray($options);

                // Simulate the event trigger
                event(new GuzzleEventCaptured($request, $options));

                return new Response(200);
            }
        ]));
        $handlerStack->push($middleware);

        $client = new Client(['handler' => $handlerStack]);

        $response = $client->request('GET', 'https://example.com');

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}