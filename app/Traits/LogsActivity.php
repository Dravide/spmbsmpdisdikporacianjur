<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        // Log created event
        static::created(function ($model) {
            $model->logActivity('created', 'Membuat data '.class_basename($model), [
                'attributes' => $model->getAttributes(),
            ]);
        });

        // Log updated event
        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $original = collect($model->getOriginal())->only(array_keys($dirty))->toArray();

            // Skip if only timestamps changed
            $skipFields = ['updated_at', 'created_at', 'remember_token'];
            $changedFields = array_diff(array_keys($dirty), $skipFields);

            if (count($changedFields) > 0) {
                $model->logActivity('updated', 'Mengubah data '.class_basename($model), [
                    'old' => $original,
                    'new' => collect($dirty)->except($skipFields)->toArray(),
                ]);
            }
        });

        // Log deleted event
        static::deleted(function ($model) {
            $model->logActivity('deleted', 'Menghapus data '.class_basename($model), [
                'attributes' => $model->getAttributes(),
            ]);
        });
    }

    /**
     * Log an activity for this model
     */
    public function logActivity($action, $description, $properties = null)
    {
        $causer = auth()->user() ?? auth('siswa')->user();

        return ActivityLog::create([
            'log_type' => 'activity',
            'action' => $action,
            'description' => $description,
            'subject_type' => get_class($this),
            'subject_id' => $this->getKey(),
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer ? $causer->getKey() : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get all activity logs for this model
     */
    public function activityLogs()
    {
        return ActivityLog::forSubject($this)->orderByDesc('created_at');
    }
}
