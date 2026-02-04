<?php

namespace App\Http\Controllers;

use App\Models\LoginSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        // Remove login session
        LoginSession::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->delete();

        if (Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }

    public function redirectToDashboard()
    {
        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'opsd' => redirect()->route('opsd.dashboard'),
            'opsmp' => redirect()->route('opsmp.dashboard'),
            'cmb' => redirect()->route('cmb.dashboard'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Force login - terminate all other sessions and login.
     */
    public function forceLogin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $user = \App\Models\User::find($request->user_id);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Get all session IDs for this user
        $sessionIds = $user->loginSessions()->pluck('session_id')->toArray();

        // Delete from Laravel sessions table
        if (!empty($sessionIds)) {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->whereIn('id', $sessionIds)
                ->delete();
        }

        // Delete all login sessions for this user
        $terminatedCount = $user->loginSessions()->delete();

        // Log the force logout activity
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'log_type' => 'login',
                'action' => 'force_logout',
                'description' => "Force logout {$terminatedCount} sesi dari perangkat lain",
                'causer_type' => get_class($user),
                'causer_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Regenerate session first
        $request->session()->regenerate();

        // Login the user
        Auth::guard('web')->login($user, $request->boolean('remember'));

        // Create new login session
        LoginSession::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => LoginSession::parseDeviceName($request->userAgent()),
            'location' => null,
            'last_activity' => now(),
        ]);

        // Redirect based on role
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'opsd' => redirect()->route('opsd.dashboard'),
            'opsmp' => redirect()->route('opsmp.dashboard'),
            'cmb' => redirect()->route('cmb.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
