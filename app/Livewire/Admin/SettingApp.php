<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

#[Title('Pengaturan Aplikasi')]
class SettingApp extends Component
{
    use WithFileUploads;

    public $app_name;
    public $app_logo_text;
    public $app_logo_image;
    public $existing_logo_image;
    public $app_logo_text_image;
    public $existing_logo_text_image;

    public function mount()
    {
        $this->app_name = get_setting('app_name', 'SPMB Cianjur');
        $this->app_logo_text = get_setting('app_logo_text', 'SPMB');
        $this->existing_logo_image = get_setting('app_logo_image');
        $this->existing_logo_text_image = get_setting('app_logo_text_image');
    }

    public function save()
    {
        $this->validate([
            'app_name' => 'required|string|max:255',
            'app_logo_text' => 'nullable|string|max:50',
            'app_logo_image' => 'nullable|image|max:2048', // 2MB Max
            'app_logo_text_image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        Setting::updateOrCreate(['key' => 'app_name'], ['value' => $this->app_name]);
        Setting::updateOrCreate(['key' => 'app_logo_text'], ['value' => $this->app_logo_text]);

        if ($this->app_logo_image) {
            // Delete old image if exists
            if ($this->existing_logo_image) {
                Storage::disk('public')->delete($this->existing_logo_image);
            }

            $path = $this->app_logo_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'app_logo_image'], ['value' => $path]);
            $this->existing_logo_image = $path;
        }

        if ($this->app_logo_text_image) {
            // Delete old image if exists
            if ($this->existing_logo_text_image) {
                Storage::disk('public')->delete($this->existing_logo_text_image);
            }

            $path = $this->app_logo_text_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'app_logo_text_image'], ['value' => $path]);
            $this->existing_logo_text_image = $path;
        }

        $this->dispatch('saved', message: 'Pengaturan berhasil disimpan.');

        // Force refresh to update layout
        return redirect()->route('admin.settings');
    }

    public function render()
    {
        return view('livewire.admin.setting-app');
    }
}
