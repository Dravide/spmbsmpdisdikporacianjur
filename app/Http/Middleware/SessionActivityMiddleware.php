<?php

namespace App\Http\Middleware;

use App\Models\LoginSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            // Check if user is from 'web' guard (App\Models\User)
            // Session tracking logic is primarily for admin/operators users
            if ($user instanceof \App\Models\User) {
                $sessionId = session()->getId();

                // Find the login session for this user and session
                $loginSession = LoginSession::where('user_id', $user->id)
                    ->where('session_id', $sessionId)
                    ->first();

                if ($loginSession) {
                    // Check if session is expired
                    if ($loginSession->isExpired()) {
                        \Illuminate\Support\Facades\Auth::guard('web')->logout();
                        session()->invalidate();
                        session()->regenerateToken();

                        return redirect()->route('login')
                            ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                    }

                    // Update last activity
                    $loginSession->update(['last_activity' => now()]);
                }
            }
        }

        return $next($request);
    }
}
