<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_type',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject of the activity (the model that was affected)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity (user who performed the action)
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by log type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('log_type', $type);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeOfAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by subject
     */
    public function scopeForSubject($query, $subject)
    {
        return $query->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey());
    }

    /**
     * Scope for filtering by causer
     */
    public function scopeCausedBy($query, $causer)
    {
        return $query->where('causer_type', get_class($causer))
            ->where('causer_id', $causer->getKey());
    }

    /**
     * Get formatted action label
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'created' => 'Membuat',
            'updated' => 'Mengubah',
            'deleted' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'force_logout' => 'Force Logout',
            'verified' => 'Memverifikasi',
            'rejected' => 'Menolak',
            'approved' => 'Menyetujui',
            'exported' => 'Mengekspor',
            'imported' => 'Mengimpor',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'force_logout' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
            'approved' => 'success',
            'exported' => 'warning',
            'imported' => 'info',
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Log an activity
     */
    public static function log($action, $description, $subject = null, $properties = null)
    {
        $causer = auth()->user() ?? auth('siswa')->user();

        return static::create([
            'log_type' => 'activity',
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer ? $causer->getKey() : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a login event
     */
    public static function logLogin($user)
    {
        return static::create([
            'log_type' => 'login',
            'action' => 'login',
            'description' => 'Login ke sistem',
            'causer_type' => get_class($user),
            'causer_id' => $user->getKey(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log a logout event
     */
    public static function logLogout($user)
    {
        return static::create([
            'log_type' => 'login',
            'action' => 'logout',
            'description' => 'Logout dari sistem',
            'causer_type' => get_class($user),
            'causer_id' => $user->getKey(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
