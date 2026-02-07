<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Pengaturan SEO')]
class SettingSeo extends Component
{
    use WithFileUploads;

    // Active Tab
    public $activeTab = 'favicon';

    // Favicon Settings
    public $favicon_16;

    public $favicon_32;

    public $favicon_180;

    public $favicon_192;

    public $favicon_512;

    public $existing_favicon_16;

    public $existing_favicon_32;

    public $existing_favicon_180;

    public $existing_favicon_192;

    public $existing_favicon_512;

    // OpenGraph Settings
    public $og_title;

    public $og_description;

    public $og_image;

    public $existing_og_image;

    public $og_type = 'website';

    public $og_locale = 'id_ID';

    public $twitter_card = 'summary_large_image';

    public $twitter_site;

    // SEO Meta Settings
    public $meta_description;

    public $meta_keywords;

    public $canonical_url;

    // Google Integration
    public $google_site_verification;

    public $google_analytics_id;

    public $google_tag_manager_id;

    // Advanced
    public $robots_txt;

    public $sitemap_enabled = true;

    public function mount()
    {
        // Load Favicon Settings
        $this->existing_favicon_16 = get_setting('favicon_16');
        $this->existing_favicon_32 = get_setting('favicon_32');
        $this->existing_favicon_180 = get_setting('favicon_180');
        $this->existing_favicon_192 = get_setting('favicon_192');
        $this->existing_favicon_512 = get_setting('favicon_512');

        // Load OpenGraph Settings
        $this->og_title = get_setting('og_title', get_setting('app_name', 'SPMB Disdikpora Cianjur'));
        $this->og_description = get_setting('og_description', 'Sistem Penerimaan Murid Baru Disdikpora Kabupaten Cianjur');
        $this->existing_og_image = get_setting('og_image');
        $this->og_type = get_setting('og_type', 'website');
        $this->og_locale = get_setting('og_locale', 'id_ID');
        $this->twitter_card = get_setting('twitter_card', 'summary_large_image');
        $this->twitter_site = get_setting('twitter_site');

        // Load SEO Meta Settings
        $this->meta_description = get_setting('meta_description', 'Sistem Penerimaan Murid Baru Disdikpora Kabupaten Cianjur');
        $this->meta_keywords = get_setting('meta_keywords', 'SPMB, Cianjur, Pendaftaran, Sekolah, SMP, Disdikpora');
        $this->canonical_url = get_setting('canonical_url', config('app.url'));

        // Load Google Integration
        $this->google_site_verification = get_setting('google_site_verification');
        $this->google_analytics_id = get_setting('google_analytics_id');
        $this->google_tag_manager_id = get_setting('google_tag_manager_id');

        // Load Advanced Settings
        $this->robots_txt = get_setting('robots_txt', $this->getDefaultRobotsTxt());
        $this->sitemap_enabled = get_setting('sitemap_enabled', '1') === '1';
    }

    private function getDefaultRobotsTxt()
    {
        return "User-agent: *\nAllow: /\n\nSitemap: ".config('app.url').'/sitemap.xml';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function saveFavicon()
    {
        $this->validate([
            'favicon_16' => 'nullable|image|max:512|dimensions:width=16,height=16',
            'favicon_32' => 'nullable|image|max:512|dimensions:width=32,height=32',
            'favicon_180' => 'nullable|image|max:1024|dimensions:width=180,height=180',
            'favicon_192' => 'nullable|image|max:1024|dimensions:width=192,height=192',
            'favicon_512' => 'nullable|image|max:2048|dimensions:width=512,height=512',
        ], [
            'favicon_16.dimensions' => 'Favicon 16x16 harus berukuran tepat 16x16 pixel',
            'favicon_32.dimensions' => 'Favicon 32x32 harus berukuran tepat 32x32 pixel',
            'favicon_180.dimensions' => 'Apple Touch Icon harus berukuran tepat 180x180 pixel',
            'favicon_192.dimensions' => 'Android Chrome Icon harus berukuran tepat 192x192 pixel',
            'favicon_512.dimensions' => 'Android Chrome Icon 512 harus berukuran tepat 512x512 pixel',
        ]);

        $this->saveImage('favicon_16', 'favicon_16', 'favicons');
        $this->saveImage('favicon_32', 'favicon_32', 'favicons');
        $this->saveImage('favicon_180', 'favicon_180', 'favicons');
        $this->saveImage('favicon_192', 'favicon_192', 'favicons');
        $this->saveImage('favicon_512', 'favicon_512', 'favicons');

        session()->flash('message', 'Pengaturan Favicon berhasil disimpan.');
    }

    public function saveOpenGraph()
    {
        $this->validate([
            'og_title' => 'required|string|max:100',
            'og_description' => 'required|string|max:300',
            'og_image' => 'nullable|image|max:2048',
            'og_type' => 'required|in:website,article,product',
            'og_locale' => 'required|string|max:10',
            'twitter_card' => 'required|in:summary,summary_large_image',
            'twitter_site' => 'nullable|string|max:50',
        ]);

        $this->saveSetting('og_title', $this->og_title);
        $this->saveSetting('og_description', $this->og_description);
        $this->saveSetting('og_type', $this->og_type);
        $this->saveSetting('og_locale', $this->og_locale);
        $this->saveSetting('twitter_card', $this->twitter_card);
        $this->saveSetting('twitter_site', $this->twitter_site);

        $this->saveImage('og_image', 'og_image', 'seo');

        session()->flash('message', 'Pengaturan OpenGraph berhasil disimpan.');
    }

    public function saveSeoMeta()
    {
        $this->validate([
            'meta_description' => 'required|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'required|url',
        ]);

        $this->saveSetting('meta_description', $this->meta_description);
        $this->saveSetting('meta_keywords', $this->meta_keywords);
        $this->saveSetting('canonical_url', $this->canonical_url);

        session()->flash('message', 'Pengaturan SEO Meta berhasil disimpan.');
    }

    public function saveGoogleIntegration()
    {
        $this->validate([
            'google_site_verification' => 'nullable|string|max:100',
            'google_analytics_id' => 'nullable|string|max:50|regex:/^(G-|UA-)[A-Z0-9]+$/i',
            'google_tag_manager_id' => 'nullable|string|max:50|regex:/^GTM-[A-Z0-9]+$/i',
        ], [
            'google_analytics_id.regex' => 'Format Google Analytics ID tidak valid (contoh: G-XXXXXXXX atau UA-XXXXXXXX)',
            'google_tag_manager_id.regex' => 'Format Google Tag Manager ID tidak valid (contoh: GTM-XXXXXXX)',
        ]);

        $this->saveSetting('google_site_verification', $this->google_site_verification);
        $this->saveSetting('google_analytics_id', $this->google_analytics_id);
        $this->saveSetting('google_tag_manager_id', $this->google_tag_manager_id);

        session()->flash('message', 'Pengaturan Google Integration berhasil disimpan.');
    }

    public function saveAdvanced()
    {
        $this->validate([
            'robots_txt' => 'nullable|string|max:2000',
            'sitemap_enabled' => 'boolean',
        ]);

        $this->saveSetting('robots_txt', $this->robots_txt);
        $this->saveSetting('sitemap_enabled', $this->sitemap_enabled ? '1' : '0');

        // Write robots.txt to public folder
        $this->writeRobotsTxt();

        session()->flash('message', 'Pengaturan Advanced berhasil disimpan.');
    }

    private function writeRobotsTxt()
    {
        $path = public_path('robots.txt');
        file_put_contents($path, $this->robots_txt);
    }

    private function saveImage($property, $key, $folder)
    {
        if ($this->$property) {
            $existingKey = 'existing_'.$property;

            // Delete old image if exists
            if ($this->$existingKey) {
                Storage::disk('public')->delete($this->$existingKey);
            }

            $path = $this->$property->store($folder, 'public');
            Setting::updateOrCreate(['key' => $key], ['value' => $path]);
            Cache::forget('setting_'.$key);
            $this->$existingKey = $path;
            $this->$property = null;
        }
    }

    private function saveSetting($key, $value)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('setting_'.$key);
    }

    public function deleteFavicon($key)
    {
        $existingKey = 'existing_'.$key;
        if ($this->$existingKey) {
            Storage::disk('public')->delete($this->$existingKey);
            Setting::where('key', $key)->delete();
            Cache::forget('setting_'.$key);
            $this->$existingKey = null;
            session()->flash('message', 'Favicon berhasil dihapus.');
        }
    }

    public function deleteOgImage()
    {
        if ($this->existing_og_image) {
            Storage::disk('public')->delete($this->existing_og_image);
            Setting::where('key', 'og_image')->delete();
            Cache::forget('setting_og_image');
            $this->existing_og_image = null;
            session()->flash('message', 'OG Image berhasil dihapus.');
        }
    }

    public function render()
    {
        return view('livewire.admin.setting-seo');
    }
}
