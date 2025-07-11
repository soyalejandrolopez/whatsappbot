<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SecurityService
{
    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = [], string $level = 'warning')
    {
        $logData = [
            'event' => $event,
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'data' => $data
        ];

        Log::log($level, "Security Event: {$event}", $logData);

        // Guardar en base de datos para eventos críticos
        if (in_array($level, ['error', 'critical', 'alert', 'emergency'])) {
            $this->saveSecurityEvent($event, $logData);
        }
    }

    /**
     * Save security event to database
     */
    protected function saveSecurityEvent(string $event, array $data)
    {
        try {
            DB::table('security_events')->insert([
                'event_type' => $event,
                'event_data' => json_encode($data),
                'ip_address' => $data['ip'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save security event to database', [
                'error' => $e->getMessage(),
                'event' => $event
            ]);
        }
    }

    /**
     * Check for suspicious activity
     */
    public function checkSuspiciousActivity(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // Verificar múltiples intentos fallidos
        if ($this->hasMultipleFailedAttempts($ip)) {
            $this->logSecurityEvent('multiple_failed_attempts', [
                'ip' => $ip,
                'attempts' => $this->getFailedAttempts($ip)
            ], 'error');
            return true;
        }

        // Verificar patrones de bot
        if ($this->isSuspiciousUserAgent($userAgent)) {
            $this->logSecurityEvent('suspicious_user_agent', [
                'user_agent' => $userAgent
            ], 'warning');
            return true;
        }

        // Verificar frecuencia de requests
        if ($this->hasHighRequestFrequency($ip)) {
            $this->logSecurityEvent('high_request_frequency', [
                'ip' => $ip,
                'frequency' => $this->getRequestFrequency($ip)
            ], 'warning');
            return true;
        }

        return false;
    }

    /**
     * Check for multiple failed attempts
     */
    protected function hasMultipleFailedAttempts(string $ip): bool
    {
        $key = "failed_attempts:{$ip}";
        $attempts = Cache::get($key, 0);
        return $attempts >= 5;
    }

    /**
     * Get failed attempts count
     */
    protected function getFailedAttempts(string $ip): int
    {
        $key = "failed_attempts:{$ip}";
        return Cache::get($key, 0);
    }

    /**
     * Record failed attempt
     */
    public function recordFailedAttempt(string $ip)
    {
        $key = "failed_attempts:{$ip}";
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addHours(1));

        if ($attempts >= 5) {
            $this->logSecurityEvent('failed_attempt_threshold_reached', [
                'ip' => $ip,
                'attempts' => $attempts
            ], 'error');
        }
    }

    /**
     * Clear failed attempts
     */
    public function clearFailedAttempts(string $ip)
    {
        $key = "failed_attempts:{$ip}";
        Cache::forget($key);
    }

    /**
     * Check for suspicious user agent
     */
    protected function isSuspiciousUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        $suspiciousPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/curl/i',
            '/wget/i',
            '/python/i',
            '/php/i'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for high request frequency
     */
    protected function hasHighRequestFrequency(string $ip): bool
    {
        $key = "request_frequency:{$ip}";
        $requests = Cache::get($key, 0);
        return $requests > 100; // Más de 100 requests por minuto
    }

    /**
     * Get request frequency
     */
    protected function getRequestFrequency(string $ip): int
    {
        $key = "request_frequency:{$ip}";
        return Cache::get($key, 0);
    }

    /**
     * Record request
     */
    public function recordRequest(string $ip)
    {
        $key = "request_frequency:{$ip}";
        $requests = Cache::get($key, 0) + 1;
        Cache::put($key, $requests, now()->addMinute());
    }

    /**
     * Validate input data
     */
    public function validateInput(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                if (str_contains($rule, 'required')) {
                    $errors[$field] = "Field {$field} is required";
                }
                continue;
            }

            $value = $data[$field];

            // Validar longitud máxima
            if (preg_match('/max:(\d+)/', $rule, $matches)) {
                $maxLength = (int) $matches[1];
                if (strlen($value) > $maxLength) {
                    $errors[$field] = "Field {$field} exceeds maximum length of {$maxLength}";
                }
            }

            // Validar caracteres peligrosos
            if ($this->containsDangerousCharacters($value)) {
                $errors[$field] = "Field {$field} contains dangerous characters";
                $this->logSecurityEvent('dangerous_input_detected', [
                    'field' => $field,
                    'value' => $value
                ], 'error');
            }

            // Validar inyección SQL
            if ($this->containsSQLInjection($value)) {
                $errors[$field] = "Field {$field} contains potential SQL injection";
                $this->logSecurityEvent('sql_injection_attempt', [
                    'field' => $field,
                    'value' => $value
                ], 'critical');
            }

            // Validar XSS
            if ($this->containsXSS($value)) {
                $errors[$field] = "Field {$field} contains potential XSS";
                $this->logSecurityEvent('xss_attempt', [
                    'field' => $field,
                    'value' => $value
                ], 'error');
            }
        }

        return $errors;
    }

    /**
     * Check for dangerous characters
     */
    protected function containsDangerousCharacters(string $value): bool
    {
        $dangerousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/eval\s*\(/i',
            '/expression\s*\(/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for SQL injection
     */
    protected function containsSQLInjection(string $value): bool
    {
        $sqlPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/or\s+1\s*=\s*1/i',
            '/and\s+1\s*=\s*1/i',
            '/\'\s*or\s*\'/i',
            '/\'\s*and\s*\'/i'
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for XSS
     */
    protected function containsXSS(string $value): bool
    {
        $xssPatterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/on\w+\s*=\s*["\'][^"\']*["\']?/i'
        ];

        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize input
     */
    public function sanitizeInput(string $value): string
    {
        // Remover tags HTML peligrosos
        $value = strip_tags($value, '<p><br><strong><em><u>');
        
        // Escapar caracteres especiales
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remover caracteres de control
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        return trim($value);
    }

    /**
     * Generate secure token
     */
    public function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Verify token
     */
    public function verifyToken(string $token, string $expectedToken): bool
    {
        return hash_equals($expectedToken, $token);
    }

    /**
     * Block IP address
     */
    public function blockIP(string $ip, int $minutes = 60)
    {
        $key = "blocked_ip:{$ip}";
        Cache::put($key, true, now()->addMinutes($minutes));
        
        $this->logSecurityEvent('ip_blocked', [
            'ip' => $ip,
            'duration_minutes' => $minutes
        ], 'warning');
    }

    /**
     * Check if IP is blocked
     */
    public function isIPBlocked(string $ip): bool
    {
        $key = "blocked_ip:{$ip}";
        return Cache::has($key);
    }

    /**
     * Unblock IP address
     */
    public function unblockIP(string $ip)
    {
        $key = "blocked_ip:{$ip}";
        Cache::forget($key);
        
        $this->logSecurityEvent('ip_unblocked', [
            'ip' => $ip
        ], 'info');
    }
}
