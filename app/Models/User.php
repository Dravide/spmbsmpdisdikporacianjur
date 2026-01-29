<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'sekolah_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the login sessions for the user.
     */
    public function loginSessions(): HasMany
    {
        return $this->hasMany(LoginSession::class);
    }

    /**
     * Get the sekolah dasar that the user (opsd) operates.
     */
    public function sekolahDasar(): BelongsTo
    {
        return $this->belongsTo(SekolahDasar::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get the sekolah smp that the user (opsmp) operates.
     */
    public function sekolahMenengahPertama(): BelongsTo
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get the sekolah linked to the user based on role
     */
    public function getSekolahAttribute()
    {
        if ($this->role === 'opsd') {
            return $this->sekolahDasar;
        } elseif ($this->role === 'opsmp') {
            return $this->sekolahMenengahPertama;
        }
        return null;
    }

    /**
     * Get the role setting for this user's role.
     */
    public function roleSetting()
    {
        return RoleSetting::where('role', $this->role)->first();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is operator SD.
     */
    public function isOpsd(): bool
    {
        return $this->role === 'opsd';
    }

    /**
     * Check if user is operator SMP.
     */
    public function isOpsmp(): bool
    {
        return $this->role === 'opsmp';
    }

    /**
     * Check if user is calon murid baru.
     */
    public function isCmb(): bool
    {
        return $this->role === 'cmb';
    }

    /**
     * Get role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'opsd' => 'Operator SD',
            'opsmp' => 'Operator SMP',
            'cmb' => 'Calon Murid Baru',
            default => ucfirst($this->role),
        };
    }

    /**
     * Get active login locations count.
     */
    public function activeLocationCount(): int
    {
        return $this->loginSessions()
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Check if user can login from a new location.
     */
    public function canLoginFromNewLocation(string $ipAddress): bool
    {
        $roleSetting = $this->roleSetting();

        if (!$roleSetting) {
            return true;
        }

        // Check if this IP is already registered
        $existingSession = $this->loginSessions()
            ->where('ip_address', $ipAddress)
            ->first();

        if ($existingSession) {
            return true;
        }

        // Check if we've reached the max locations
        return $this->activeLocationCount() < $roleSetting->max_login_locations;
    }
}
