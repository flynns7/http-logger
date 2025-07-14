<?php
// app/Logging/CustomHttpLogger.php

namespace Flynns7\HttpLogger\Logging;

use Monolog\Logger;

class CustomHttpLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('http');

        $logger->pushHandler(new HttpLogHandler(
            Logger::toMonologLevel($config['level'] ?? 'debug')
        ));

        return $logger;
    }
}
