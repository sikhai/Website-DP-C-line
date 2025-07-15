<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyQrApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');
        $validKey = env('QR_API_KEY');

        if ($apiKey !== $validKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
