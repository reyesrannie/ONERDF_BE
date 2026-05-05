<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header("API_KEY"); // You can change the header name if needed

        // Check if the API key matches your expected key
        if ($apiKey !== env("API_KEY")) {
            return response()->json(["error" => "Unauthorized"], 401);
        }

        return $next($request);
    }
}
