<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
{
    return $next($request)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')  // Gantilah dengan domain frontend yang sesuai
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization')
        ->header('Access-Control-Allow-Credentials', 'true');
}

    
}
