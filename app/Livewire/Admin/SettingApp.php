<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Pengaturan Aplikasi')]
class SettingApp extends Component
{
    use WithFileUploads;

    public $app_name;

    public $app_logo_text;

    public $site_mode; // normal, maintenance, coming_soon

    public $app_logo_image;

    public $existing_logo_image;

    public $app_logo_text_image;

    public $existing_logo_text_image;

    public $active_ticket_types = [];

    public $ticketTypes = [
        'delete_pendaftaran' => 'Hapus Pendaftaran',
        'reset_password' => 'Reset Password Siswa',
        'move_jalur' => 'Pindah Jalur Pendaftaran',
        'unverify' => 'Buka Kunci Verifikasi',
        'correction_data' => 'Koreksi Data (NIK/NISN)',
        'restore_pendaftaran' => 'Restore / Batalkan Hapus',
        'delete_file' => 'Request Hapus Berkas',
        'transfer_school' => 'Pindah Sekolah Pilihan',
        'input_sekolah_dasar' => 'Input Sekolah Dasar Baru',
    ];


    public function mount()
    {
        $this->app_name = get_setting('app_name', 'SPMB Cianjur');
        $this->app_logo_text = get_setting('app_logo_text', 'SPMB');
        $this->site_mode = get_setting('site_mode', 'normal');
        $this->existing_logo_image = get_setting('app_logo_image');
        $this->existing_logo_text_image = get_setting('app_logo_text_image');

        $savedTicketTypes = get_setting('active_ticket_types');
        // Default to all active if not set, or empty array if explicitly set to empty? 
        // Better default to ALL if null.
        if ($savedTicketTypes === null) {
            $this->active_ticket_types = array_keys($this->ticketTypes);
        } else {
            $this->active_ticket_types = json_decode($savedTicketTypes, true) ?? [];
        }
    }

    public function save()
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'app_logo_text' => 'nullable|string|max:50',
            'site_mode' => 'required|in:normal,maintenance,coming_soon',
            'app_logo_image' => 'nullable|image|max:2048', // 2MB Max
            'app_logo_text_image' => 'nullable|image|max:2048', // 2MB Max
            'active_ticket_types' => 'array',
        ]);

        Setting::updateOrCreate(['key' => 'app_name'], ['value' => $this->app_name]);
        \Illuminate\Support\Facades\Cache::forget('setting_app_name');

        Setting::updateOrCreate(['key' => 'app_logo_text'], ['value' => $this->app_logo_text]);
        \Illuminate\Support\Facades\Cache::forget('setting_app_logo_text');

        Setting::updateOrCreate(['key' => 'site_mode'], ['value' => $this->site_mode]);
        \Illuminate\Support\Facades\Cache::forget('setting_site_mode');

        if ($this->app_logo_image) {
            // Delete old image if exists
            if ($this->existing_logo_image) {
                Storage::disk('public')->delete($this->existing_logo_image);
            }

            $path = $this->app_logo_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'app_logo_image'], ['value' => $path]);
            \Illuminate\Support\Facades\Cache::forget('setting_app_logo_image');
            $this->existing_logo_image = $path;
        }

        if ($this->app_logo_text_image) {
            // Delete old image if exists
            if ($this->existing_logo_text_image) {
                Storage::disk('public')->delete($this->existing_logo_text_image);
            }

            $path = $this->app_logo_text_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'app_logo_text_image'], ['value' => $path]);
            \Illuminate\Support\Facades\Cache::forget('setting_app_logo_text_image');
            $this->existing_logo_text_image = $path;
        }

        Setting::updateOrCreate(['key' => 'active_ticket_types'], ['value' => json_encode($this->active_ticket_types)]);
        \Illuminate\Support\Facades\Cache::forget('setting_active_ticket_types');

        $this->dispatch('saved', message: 'Pengaturan berhasil disimpan.');

        // Force refresh to update layout
        return redirect()->route('admin.settings');
    }

    public function render()
    {
        return view('livewire.admin.setting-app');
    }
}
