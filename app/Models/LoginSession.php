<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginSession extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_name',
        'location',
        'session_id',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    /**
     * Get the user that owns the login session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse device name from user agent.
     */
    public static function parseDeviceName(string $userAgent): string
    {
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows PC';
        } elseif (str_contains($userAgent, 'Macintosh')) {
            return 'Mac';
        } elseif (str_contains($userAgent, 'iPhone')) {
            return 'iPhone';
        } elseif (str_contains($userAgent, 'iPad')) {
            return 'iPad';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        }

        return 'Unknown Device';
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity(): bool
    {
        $this->last_activity = now();

        return $this->save();
    }

    /**
     * Check if session is expired based on role settings.
     */
    public function isExpired(): bool
    {
        $roleSetting = $this->user->roleSetting();

        if (! $roleSetting) {
            return false;
        }

        $timeoutMinutes = $roleSetting->session_timeout_minutes;

        return $this->last_activity->addMinutes($timeoutMinutes)->isPast();
    }
}
