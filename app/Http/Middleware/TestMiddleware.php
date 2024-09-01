<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class TestMiddleware
{
    public function handle($request, Closure $next)
    {
        Log::info('TestMiddleware has been executed.');
        return $next($request);
    }
}