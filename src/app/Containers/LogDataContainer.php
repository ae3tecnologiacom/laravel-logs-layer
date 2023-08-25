<?php

namespace Ae3\LaravelLogsLayer\app\Containers;

class LogDataContainer
{
    /**
     * @var array
     */
    private array $capturedQueries = [];
    /**
     * @var array
     */
    private array $capturedHttpClientEvents = [];

    /**
     * @param array $queryInfo
     * @return void
     */
    public function addCapturedQuery(array $queryInfo)
    {
        $this->capturedQueries[] = $queryInfo;
    }

    /**
     * @param array $httpClientEvent
     * @return void
     */
    public function addCapturedHttpClientEvent(array $httpClientEvent)
    {
        $this->capturedHttpClientEvents[] = $httpClientEvent;
    }

    /**
     * @return array
     */
    public function getCapturedHttpClientEvents(): array
    {
        return $this->capturedHttpClientEvents;
    }

    /**
     * @return array
     */
    public function getCapturedQueries(): array
    {
        return $this->capturedQueries;
    }

    /**
     * @return void
     */
    public function clearCapturedData()
    {
        $this->capturedQueries = [];
        $this->capturedHttpClientEvents = [];
    }
}