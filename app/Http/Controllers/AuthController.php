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
}
