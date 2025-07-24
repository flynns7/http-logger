<?php

namespace Flynns7\HttpLogger;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/api-logger.php', 'api-logger');
        $this->commands([
            \Flynns7\HttpLogger\Console\Commands\SyncRoutesMappingCommand::class,
            \Flynns7\HttpLogger\Console\Commands\InstallHttpLoggerCommand::class,
        ]);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/api-logger.php' => config_path('api-logger.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'http-logger-migrations');
        
        // Optional: register custom channel
        $this->app['log']->extend('http', function ($app, $config) {
            return new \Monolog\Logger('http', [
                new Logging\HttpLogHandler()
            ]);
        });
    }
}
