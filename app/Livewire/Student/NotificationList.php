<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Semua Notifikasi')]
class NotificationList extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $user = auth()->user() ?? auth('siswa')->user();
        if ($user) {
            $notification = $user->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
            }
        }
    }

    public function markAllAsRead()
    {
        $user = auth()->user() ?? auth('siswa')->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            session()->flash('message', 'Semua notifikasi ditandai sudah dibaca.');
        }
    }

    public function render()
    {
        $user = auth()->user() ?? auth('siswa')->user();
        $notifications = $user ? $user->notifications()->paginate(10) : collect();

        return view('livewire.student.notification-list', [
            'notifications' => $notifications,
        ]);
    }
}
