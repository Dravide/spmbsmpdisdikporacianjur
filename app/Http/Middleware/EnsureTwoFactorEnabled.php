<?php

namespace App\Http\Middleware;

use App\Models\RoleSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Check if role requires 2FA
        $roleSetting = RoleSetting::getByRole($user->role);
        if (! $roleSetting || ! $roleSetting->two_factor_required) {
            return $next($request);
        }

        // Exclude 2FA routes to prevent loops
        if ($request->routeIs('two-factor.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Check if user has enabled 2FA
        if (! $user->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('two-factor.setup');
        }

        // Check if session is verified
        if (! $request->session()->get('two_factor_verified')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
