<?php

namespace Flynns7\HttpLogger\Traits;

use Illuminate\Support\Facades\Log;

trait Logging
{

    protected function log($request, $response)
    {
        $start = $request->attributes->get('start');
        $end = $request->attributes->get('end');
        $duration = $end - $start;
        $context = array(
            'request' => $request->all(),
            'response' => $this->extractResponseContent($response),
            'processing_time' => $duration, // Convert to milliseconds
        );
        Log::channel('http')->info("Request Log", $context);
    }

    protected function extractResponseContent($response)
    {
        if (!method_exists($response, 'getContent')) {
            return 'No content';
        }

        $content = $response->getContent();
        $contentType = $response->headers->get('Content-Type');

        // Check if content is JSON
        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode($content, true);

            // If decoding successful, return as array
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            // Invalid JSON? Return raw string as fallback
            return $content;
        }

        // For HTML or other types, return string
        return $content;
    }
}
