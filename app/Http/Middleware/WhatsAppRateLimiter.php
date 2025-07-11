<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtener identificador único (número de teléfono o IP)
        $identifier = $this->getIdentifier($request);

        // Verificar rate limits
        if ($this->isRateLimited($identifier)) {
            Log::warning('WhatsApp rate limit exceeded', [
                'identifier' => $identifier,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'Rate limit exceeded. Please try again later.'
            ], 429);
        }

        // Incrementar contador
        $this->incrementCounter($identifier);

        return $next($request);
    }

    /**
     * Get unique identifier for rate limiting
     */
    protected function getIdentifier(Request $request): string
    {
        // Intentar obtener el número de teléfono del webhook
        $data = $request->all();

        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0]['from'])) {
            return 'whatsapp_phone:' . $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];
        }

        // Fallback a IP
        return 'whatsapp_ip:' . $request->ip();
    }

    /**
     * Check if identifier is rate limited
     */
    protected function isRateLimited(string $identifier): bool
    {
        $limits = config('whatsapp.rate_limit');

        // Verificar límite por segundo
        $perSecondKey = "rate_limit:second:{$identifier}";
        $perSecondCount = Cache::get($perSecondKey, 0);
        if ($perSecondCount >= $limits['messages_per_second']) {
            return true;
        }

        // Verificar límite por minuto
        $perMinuteKey = "rate_limit:minute:{$identifier}";
        $perMinuteCount = Cache::get($perMinuteKey, 0);
        if ($perMinuteCount >= $limits['messages_per_minute']) {
            return true;
        }

        // Verificar límite por hora
        $perHourKey = "rate_limit:hour:{$identifier}";
        $perHourCount = Cache::get($perHourKey, 0);
        if ($perHourCount >= $limits['messages_per_hour']) {
            return true;
        }

        return false;
    }

    /**
     * Increment rate limit counters
     */
    protected function incrementCounter(string $identifier): void
    {
        $perSecondKey = "rate_limit:second:{$identifier}";
        $perMinuteKey = "rate_limit:minute:{$identifier}";
        $perHourKey = "rate_limit:hour:{$identifier}";

        // Incrementar contadores con TTL apropiado
        Cache::increment($perSecondKey, 1);
        Cache::expire($perSecondKey, 1);

        Cache::increment($perMinuteKey, 1);
        Cache::expire($perMinuteKey, 60);

        Cache::increment($perHourKey, 1);
        Cache::expire($perHourKey, 3600);
    }
}
