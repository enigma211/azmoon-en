<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log GET requests to avoid tracking API calls or form submissions separately
        if (!$request->isMethod('get')) {
             return $next($request);
        }
        
        // Exclude admin routes and assets
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('filament*')) {
            return $next($request);
        }

        try {
            $ip = $request->ip();
            
            // Check DB directly to ensure we don't log multiple times per hour for the same IP
            // This is more robust than Cache which might be cleared or misconfigured
            $exists = VisitLog::where('ip', $ip)
                ->where('created_at', '>=', now()->subHour())
                ->exists();

            if (!$exists) {
                VisitLog::create([
                    'ip' => $ip,
                    'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                ]);
            }
        } catch (\Exception $e) {
            // Fail silently
        }

        return $next($request);
    }
}
