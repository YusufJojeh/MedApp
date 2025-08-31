<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AiAssistantService;
use Illuminate\Support\Facades\Log;

class AiServiceHealthCheck
{
    protected $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if AI service is enabled
        if (!config('services.ai.enabled', true)) {
            return $next($request);
        }

        // Check AI service health
        $health = $this->aiService->checkHealth();

        if (!$health['success']) {
            Log::warning('AI Service Health Check Failed: ' . ($health['error'] ?? 'Unknown error'));

            // If fallback is enabled, continue with reduced functionality
            if (config('services.ai.fallback_enabled', true)) {
                $request->attributes->set('ai_service_available', false);
                return $next($request);
            }

            // If no fallback, return error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI service is currently unavailable. Please try again later.',
                    'error' => 'ai_service_unavailable'
                ], 503);
            }

            return response()->view('errors.ai-service-unavailable', [], 503);
        }

        $request->attributes->set('ai_service_available', true);
        return $next($request);
    }
}
