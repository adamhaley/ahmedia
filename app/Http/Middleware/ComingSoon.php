<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoon
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('coming-soon.enabled')) {
            return $next($request);
        }

        if ($request->is('admin*', 'up', 'preview/*', 'auth/*') || $request->cookie('coming_soon_bypass') === 'granted') {
            return $next($request);
        }

        return response()->view('coming-soon', status: 503);
    }
}
