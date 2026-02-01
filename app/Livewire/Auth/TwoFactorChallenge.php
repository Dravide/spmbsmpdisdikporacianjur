<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Two-Factor Authentication Challenge')]
class TwoFactorChallenge extends Component
{
    public $code;
    public $recoveryCode;
    public $params = ['code', 'recoveryCode'];
    public $usingRecoveryCode = false;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (session()->get('two_factor_verified')) {
            return redirect()->route('dashboard');
        }

        if (!Auth::user()->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('two-factor.setup');
        }
    }

    public function verify()
    {
        $this->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ($user->verifyTwoFactorCode($this->code)) {
            session()->put('two_factor_verified', true);
            return redirect()->route('dashboard');
        }

        $this->addError('code', 'Kode autentikasi tidak valid.');
    }

    public function verifyWithRecoveryCode()
    {
        $this->validate([
            'recoveryCode' => 'required|string',
        ]);

        $user = Auth::user();
        $recoveryCodes = $user->recoveryCodes();

        if (($key = array_search($this->recoveryCode, $recoveryCodes)) !== false) {
            unset($recoveryCodes[$key]);
            $user->replaceRecoveryCodes(array_values($recoveryCodes));

            session()->put('two_factor_verified', true);
            return redirect()->route('dashboard');
        }

        $this->addError('recoveryCode', 'Kode pemulihan tidak valid.');
    }

    public function toggleRecovery()
    {
        $this->usingRecoveryCode = !$this->usingRecoveryCode;
    }

    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }
}
