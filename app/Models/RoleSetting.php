<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleSetting extends Model
{
    protected $fillable = [
        'role',
        'role_label',
        'max_login_locations',
        'session_timeout_minutes',
        'allow_multiple_sessions',
    ];

    protected $casts = [
        'max_login_locations' => 'integer',
        'session_timeout_minutes' => 'integer',
        'allow_multiple_sessions' => 'boolean',
    ];

    /**
     * Get role setting by role name.
     */
    public static function getByRole(string $role): ?self
    {
        return static::where('role', $role)->first();
    }

    /**
     * Get all role settings as array keyed by role.
     */
    public static function getAllAsArray(): array
    {
        return static::all()->keyBy('role')->toArray();
    }
}
