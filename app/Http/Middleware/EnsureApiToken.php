<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Native\Desktop\Facades\Settings;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $storedToken = Settings::get('api_token', null);
        } catch (Exception) {
            $storedToken = session('api_token');
        }

        if (! $storedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bearerToken = $request->bearerToken();

        if (! $bearerToken || ! hash_equals($storedToken, $bearerToken)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
