<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('layouts.guest')]
#[Title('Setup Two-Factor Authentication')]
class TwoFactorSetup extends Component
{
    public $qrCodeSvg;

    public $secret;

    public $code;

    public $setupComplete = false;

    public $recoveryCodes = [];

    public function mount()
    {
        $user = Auth::user();

        if ($user->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('dashboard');
        }

        if (! $this->secret) {
            $google2fa = new Google2FA;
            $this->secret = $google2fa->generateSecretKey();

            // Temporarily store secret to generate QR
            $user->two_factor_secret = Crypt::encryptString($this->secret);
            $user->save();
        }

        $this->qrCodeSvg = $user->twoFactorQrCodeSvg();
    }

    public function confirm()
    {
        $this->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ($user->verifyTwoFactorCode($this->code)) {
            // Generate Recovery Codes
            $recoveryCodes = [];
            for ($i = 0; $i < 8; $i++) {
                $recoveryCodes[] = str()->random(10).'-'.str()->random(10);
            }

            $user->replaceRecoveryCodes($recoveryCodes);
            $user->two_factor_confirmed_at = now();
            $user->save();

            // Mark session as verified
            session()->put('two_factor_verified', true);

            $this->recoveryCodes = $recoveryCodes;
            $this->setupComplete = true;
        } else {
            $this->addError('code', 'The provided code was invalid.');
        }
    }

    public function finish()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.two-factor-setup');
    }
}
