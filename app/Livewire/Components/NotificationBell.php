<?php

namespace App\Livewire\Components;

use Livewire\Component;

class NotificationBell extends Component
{
    public $showDropdown = false;

    public function getNotificationsProperty()
    {
        $user = auth()->user() ?? auth('siswa')->user();

        if (! $user) {
            return collect();
        }

        return $user->notifications()->latest()->limit(10)->get();
    }

    public function getUnreadCountProperty()
    {
        $user = auth()->user() ?? auth('siswa')->user();

        if (! $user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $user = auth()->user() ?? auth('siswa')->user();

        if ($user) {
            $user->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
        }
    }

    public function markAllAsRead()
    {
        $user = auth()->user() ?? auth('siswa')->user();

        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
