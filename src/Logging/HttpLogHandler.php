<?php

namespace Flynns7\HttpLogger\Logging;

use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Illuminate\Support\Facades\Http;
use Monolog\LogRecord;

class HttpLogHandler extends AbstractProcessingHandler
{
    protected $endpoint;
    protected string $eventId;
    protected string $userId;
    protected string $userType;
    protected string $actionNameBy;
    protected string $serviceName;


    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->endpoint = config('api-logger.endpoint');
        $this->eventId = config('api-logger.eventId');
        $this->userId = config('api-logger.userId');
        $this->userType = config('api-logger.userType');
        $this->actionNameBy = config('api-logger.actionNameBy');
        $this->serviceName = config('api-logger.service');
    }

    protected function write(LogRecord $record): void
    {
        // Send the log record to an external API via HTTP

        $level   = $record['level_name'];
        $message = $record['message'];
        $context = $record['context'];
        $extra   = $record['extra'];

        $request = request();
        $action = [
            'route_name' => $request->route()->getName(),
            'action'     => $request->route()->getActionName(),
            'uri'        => $request->route()->uri(),
        ];
        $actionName = isset(cache('http_logger_routes')[$action['uri']]) ? cache('http_logger_routes')[$action['uri']]->case_name : $action['action'];
        $user = $request->user();
        if ($user) {
            $userId = $user->id;
            $userName = $user->name;
            $this->userId = "$userName - $userId";
        }
        try {
            $payload = [
                'timestamp' => now()->toIso8601String(),
                "log_event" =>  $level,
                "service" =>  $this->serviceName,
                "environment" =>  env('APP_ENV', 'production'),
                "processing_time_ms" =>  $request->has('processing_time') ? $request->input('processing_time') : 0,
                "action" =>  $actionName,
                "result" => (strtoupper($level) == 'INFO') ? 'SUCCESS' : 'FAILED',
                "user" =>  [
                    "user_id" =>  $this->userId,
                    "user_type" =>  $this->userType,
                    "ip_address" =>  $request->getClientIp(),
                    "lat_long" =>  "",
                    "user_agent" =>  $request->userAgent()
                ],
                "request" =>  [
                    "method" =>  $request->method(),
                    "endpoint" =>  isset($context['endpoint']) ? $context['endpoint'] : $request->fullUrl(),
                    "payload" =>  isset($context['request']) ? $context['request'] : $request->all(),
                ],
                "response" =>  isset($context['response']) ? $context['response'] : ["message" => $message]
            ];
            $response = Http::post($this->endpoint, $payload);
            if ($response->status() != 200 || $response->json('code') != 200) {
                Log::error('Failed to log HTTP request', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'payload' => $payload
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Failed to log HTTP request', [
                'error' => $e->getMessage()
            ]);
            // Optional: handle logging failure (e.g., fallback to local log)
        }
    }
}
