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
                Auth::logout();
                $roleSetting = $user->roleSetting();
                $maxLocations = $roleSetting ? $roleSetting->max_login_locations : 2;
                $this->addError('username', "Anda sudah login dari {$maxLocations} lokasi berbeda. Logout dari lokasi lain terlebih dahulu.");
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
