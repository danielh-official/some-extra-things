<?php

namespace App\Http\Middleware;

use App\Support\ServerState;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiServerEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! ServerState::isEnabled()) {
            return new JsonResponse([
                'message' => 'Local Things API server is disabled.',
            ], 503);
        }

        return $next($request);
    }
}

