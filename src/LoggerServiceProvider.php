<?php
namespace Flynns7\HttpLogger;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/api-logger.php', 'api-logger');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/api-logger.php' => config_path('api-logger.php'),
        ], 'config');

        // Optional: register custom channel
        $this->app['log']->extend('http', function ($app, $config) {
            return new \Monolog\Logger('http', [
                new Logging\HttpLogHandler()
            ]);
        });
    }
}
