<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenancePage
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.maintenance_page')) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        return response()
            ->view('maintenance', [], 503)
            ->header('Retry-After', '3600');
    }

    private function shouldBypass(Request $request): bool
    {
        return $request->is(
            'admin',
            'admin/*',
            'build/*',
            'css/*',
            'js/*',
            'images/*',
            'storage/*',
            'favicon.ico',
            'robots.txt'
        );
    }
}
