<?php

namespace App\Traits;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

trait TwoFactorAuthenticatable
{
    /**
     * Determine if the user has enabled two-factor authentication.
     *
     * @return bool
     */
    public function hasEnabledTwoFactorAuthentication()
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the user's two-factor authentication recovery codes.
     *
     * @return array
     */
    public function recoveryCodes()
    {
        return json_decode(Crypt::decryptString($this->two_factor_recovery_codes), true);
    }

    /**
     * Replace the user's two-factor authentication recovery codes.
     *
     * @return void
     */
    public function replaceRecoveryCodes(array $codes)
    {
        $this->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codes)),
        ])->save();
    }

    /**
     * Get the QR code SVG for the user's two-factor authentication.
     *
     * @return string
     */
    public function twoFactorQrCodeSvg()
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0),
                new SvgImageBackEnd()
            )
        ))->writeString(
                $this->twoFactorQrCodeUrl()
            );

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two-factor authentication QR code URL for the user.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl()
    {
        $appName = config('app.name', 'SPMB Cianjur');
        return (new Google2FA)->getQRCodeUrl(
            $appName,
            $this->username ?? $this->name ?? 'User',
            $this->twoFactorSecret()
        );
    }

    /**
     * Get the decrypted two-factor secret.
     */
    public function twoFactorSecret()
    {
        return Crypt::decryptString($this->two_factor_secret);
    }

    /**
     * Verify the given code.
     */
    public function verifyTwoFactorCode($code)
    {
        return (new Google2FA)->verifyKey(
            $this->twoFactorSecret(),
            $code
        );
    }
}
