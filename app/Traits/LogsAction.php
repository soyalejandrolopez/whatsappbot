<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait LogsAction
{
    /**
     * Log a controller action in structured JSON format.
     */
    protected function logAction(Request $request, $response = null): void
    {
        // Build payload
        $payload = [
            'controller'   => static::class,
            'route_name'   => $request->route() ? $request->route()->getName() : null,
            'method_name'  => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? null,
            'http_method'  => $request->method(),
            'status'       => optional($response)->getStatusCode(),
            'user_id'      => auth()->id(),
            'ip'           => $request->ip(),
            'url'          => $request->fullUrl(),
            'payload'      => $request->except(['password', 'password_confirmation']),
        ];

        Log::channel('structured')->info('controller_action', $payload);
    }
} 