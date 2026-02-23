<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    /**
     * If no admin user exists, redirect to the installation wizard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (User::count() > 0) {
            return $next($request);
        }

        if ($request->routeIs('install') || $request->routeIs('install.store')) {
            return $next($request);
        }

        return redirect()->route('install');
    }
}
