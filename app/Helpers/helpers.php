<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('get_setting')) {
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        // Cache settings for performance (optional, but good practice)
        // For now, we'll fetch directly or simple cache
        return Cache::remember('setting_'.$key, 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }
}
