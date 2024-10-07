<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');

        $validApiKeys = [
            env('API_KEY'),
            env('API_KEY_2'),
        ];

        if (in_array($apiKey, $validApiKeys, true)) {
            return $next($request); 
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

