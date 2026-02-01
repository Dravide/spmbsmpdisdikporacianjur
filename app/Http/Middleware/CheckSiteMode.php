<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to admin, storage, and livewire routes
        if (
            $request->is('admin*') ||
            $request->is('login*') ||
            $request->is('livewire*') ||
            $request->is('storage*') ||
            $request->is('opsd*') ||
            $request->is('opsmp*') ||
            $request->is('siswa*') ||
            $request->is('two-factor*') ||
            $request->is('settings*') ||
            $request->is('sessions*') ||
            $request->is('dashboard*') ||
            $request->is('register-mandiri*') ||
            $request->is('logout')
        ) {
            return $next($request);
        }

        $mode = get_setting('site_mode', 'normal');

        if ($mode === 'maintenance') {
            return response()->view('errors.maintenance');
        }

        if ($mode === 'coming_soon') {
            return response()->view('errors.coming-soon');
        }

        return $next($request);
    }
}
