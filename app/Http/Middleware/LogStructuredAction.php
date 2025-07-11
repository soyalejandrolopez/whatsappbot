<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogStructuredAction
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Build log payload
        $payload = [
            'route_name'  => $request->route() ? $request->route()->getName() : null,
            'controller'  => optional($request->route())->getActionName(),
            'http_method' => $request->method(),
            'status'      => $response->getStatusCode(),
            'user_id'     => auth()->id(),
            'ip'          => $request->ip(),
            'url'         => $request->fullUrl(),
        ];

        Log::channel('structured')->info('request_handled', $payload);

        return $response;
    }
} 