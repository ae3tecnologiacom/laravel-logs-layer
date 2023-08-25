<?php

namespace Ae3\LaravelLogsLayer\app\Providers;

use Ae3\LaravelLogsLayer\app\Containers\LogDataContainer;
use Ae3\LaravelLogsLayer\app\Events\GuzzleEventCaptured;
use Ae3\LaravelLogsLayer\app\Listeners\GuzzleEventCapturedListener;
use Ae3\LaravelLogsLayer\app\Observers\LogCaptureObserver;
use Ae3\LaravelLogsLayer\app\Services\AbstractLogService;
use Illuminate\Support\ServiceProvider;

class LogsLayerServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'laravel-logs-layer');

        $this->app->singleton(LogDataContainer::class, function ($app) {
            return new LogDataContainer();
        });

        LogCaptureObserver::registerListeners();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/config.php' => config_path('laravel-logs-layer.php'),
            ], 'config');
        }

        $this->app->when(AbstractLogService::class)
            ->needs(LogDataContainer::class)
            ->give(LogDataContainer::class);
    }

}