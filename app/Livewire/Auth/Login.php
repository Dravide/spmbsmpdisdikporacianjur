<?php

namespace App\Livewire\Auth;

use App\Models\LoginSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Login - SPMB Disdikpora Cianjur')]
class Login extends Component
{
    #[Rule('required|string')]
    public string $username = '';

    #[Rule('required|string')]
    public string $password = '';

    public bool $remember = false;

    // Force logout modal state
    public bool $showForceLogoutModal = false;
    public ?int $pendingUserId = null;
    public array $activeSessions = [];

    public function login()
    {
        $this->validate();

        // Check rate limiting
        $throttleKey = 'login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
            return;
        }

        // Attempt authentication (Admin/Operator/CMB)
        if (Auth::guard('web')->attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            $user = Auth::guard('web')->user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                $this->addError('username', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
                return;
            }

            // Check location limit
            $ipAddress = request()->ip();
            if (!$user->canLoginFromNewLocation($ipAddress)) {
                // Store pending login data and show modal
                $this->pendingUserId = $user->id;
                $this->activeSessions = $user->loginSessions()
                    ->select('id', 'ip_address', 'device_name', 'last_activity')
                    ->orderBy('last_activity', 'desc')
                    ->get()
                    ->toArray();
                $this->showForceLogoutModal = true;

                Auth::logout();
                return;
            }

            // Clear rate limiter
            RateLimiter::clear($throttleKey);

            session()->regenerate();

            // Create login session
            LoginSession::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'session_id' => session()->getId(),
                ],
                [
                    'ip_address' => $ipAddress,
                    'user_agent' => request()->userAgent(),
                    'device_name' => LoginSession::parseDeviceName(request()->userAgent()),
                    'location' => null, // Can be enhanced with GeoIP
                    'last_activity' => now(),
                ]
            );

            // Redirect based on role
            return redirect()->intended($this->getDashboardRoute($user->role));
        }

        // Attempt authentication (Studnet) - Use 'nisn' as username
        if (Auth::guard('siswa')->attempt(['nisn' => $this->username, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            return redirect()->intended(route('siswa.dashboard'));
        }

        RateLimiter::hit($throttleKey);
        $this->addError('username', 'Username atau password salah.');
    }

    /**
     * Force logout all other sessions and proceed with login.
     */
    public function forceLogoutAndLogin()
    {
        if (!$this->pendingUserId) {
            $this->showForceLogoutModal = false;
            return;
        }

        $user = \App\Models\User::find($this->pendingUserId);

        if (!$user) {
            $this->resetForceLogoutState();
            $this->addError('username', 'User tidak ditemukan.');
            return;
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
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Now authenticate and create new session
        Auth::guard('web')->login($user, $this->remember);
        session()->regenerate();

        // Create new login session
        LoginSession::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'device_name' => LoginSession::parseDeviceName(request()->userAgent()),
            'location' => null,
            'last_activity' => now(),
        ]);

        $this->resetForceLogoutState();

        // Redirect based on role
        return redirect()->intended($this->getDashboardRoute($user->role));
    }

    /**
     * Cancel force logout and reset state.
     */
    public function cancelForceLogout()
    {
        $this->resetForceLogoutState();
    }

    /**
     * Reset force logout modal state.
     */
    protected function resetForceLogoutState()
    {
        $this->showForceLogoutModal = false;
        $this->pendingUserId = null;
        $this->activeSessions = [];
    }

    protected function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'admin' => route('admin.dashboard'),
            'opsd' => route('opsd.dashboard'),
            'opsmp' => route('opsmp.dashboard'),

            default => route('dashboard'),
        };
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
