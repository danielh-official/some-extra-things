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
            $storedHash = Settings::get('api_token_hash', null);
        } catch (Exception) {
            $storedHash = session('api_token_hash');
        }

        if (! $storedHash) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bearerToken = $request->bearerToken();

        if (! $bearerToken || ! hash_equals($storedHash, hash('sha256', $bearerToken))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
