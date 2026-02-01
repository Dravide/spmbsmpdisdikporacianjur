<?php

namespace App\Livewire\Settings;

use App\Models\RoleSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan Role')]
class RoleSettings extends Component
{
    public $settings = [];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->settings = RoleSetting::all()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'role' => $setting->role,
                'role_label' => $setting->role_label,
                'max_login_locations' => $setting->max_login_locations,
                'session_timeout_minutes' => $setting->session_timeout_minutes,
                'allow_multiple_sessions' => $setting->allow_multiple_sessions,
                'two_factor_required' => $setting->two_factor_required,
            ];
        })->toArray();
    }

    public function updateSetting($index)
    {
        $setting = $this->settings[$index];

        $validated = $this->validate([
            "settings.{$index}.max_login_locations" => 'required|integer|min:1|max:100',
            "settings.{$index}.session_timeout_minutes" => 'required|integer|min:5|max:1440',
            "settings.{$index}.two_factor_required" => 'boolean',
        ], [
            "settings.{$index}.max_login_locations.required" => 'Batas lokasi wajib diisi',
            "settings.{$index}.max_login_locations.min" => 'Minimal 1 lokasi',
            "settings.{$index}.session_timeout_minutes.required" => 'Timeout sesi wajib diisi',
            "settings.{$index}.session_timeout_minutes.min" => 'Minimal 5 menit',
        ]);

        RoleSetting::find($setting['id'])->update([
            'max_login_locations' => $setting['max_login_locations'],
            'session_timeout_minutes' => $setting['session_timeout_minutes'],
            'allow_multiple_sessions' => $setting['allow_multiple_sessions'],
            'two_factor_required' => $setting['two_factor_required'] ?? false,
        ]);

        session()->flash('success', "Pengaturan {$setting['role_label']} berhasil diperbarui!");
    }

    public function render()
    {
        return view('livewire.settings.role-settings');
    }
}
