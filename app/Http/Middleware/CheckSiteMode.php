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
        $host = $request->getHost();

        // Allow access to specific subdomains (Admin, Auth, Operators)
        if (
            str_contains($host, 'admin.') ||
            str_contains($host, 'auth.') ||
            str_contains($host, 'dash.') || // OPSMP
            str_contains($host, 'operator.') || // OPSD
            str_contains($host, 'pendaftaran.') // Siswa
        ) {
            return $next($request);
        }

        // Allow access to specific routes/paths
        if (
            $request->is('login*') ||
            $request->is('livewire*') ||
            $request->is('storage*') ||
            $request->is('two-factor*') ||
            $request->is('register*') || // Allow both /register and /register-mandiri
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
