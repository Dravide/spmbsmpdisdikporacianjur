<?php

namespace App\Livewire\Settings;

use App\Models\LoginSession;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Sesi Login Aktif')]
class ActiveSessions extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function terminateSession($sessionId)
    {
        $session = LoginSession::find($sessionId);

        if ($session) {
            // Delete from standard Laravel sessions table
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('id', $session->session_id)
                ->delete();

            // Delete log
            $session->delete();

            $this->dispatch('session-terminated');
        }
    }

    public function terminateAllUserSessions($userId)
    {
        // Get all session IDs for this user from logs
        $sessionIds = LoginSession::where('user_id', $userId)->pluck('session_id')->toArray();

        // Delete from standard sessions table
        if (!empty($sessionIds)) {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->whereIn('id', $sessionIds)
                ->delete();
        }

        $count = LoginSession::where('user_id', $userId)->delete();
        session()->flash('success', "{$count} sesi berhasil dihentikan.");
    }

    public function render()
    {
        $sessions = LoginSession::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('username', 'like', '%' . $this->search . '%');
                })->orWhere('ip_address', 'like', '%' . $this->search . '%');
            })
            ->orderBy('last_activity', 'desc')
            ->paginate(10);

        return view('livewire.settings.active-sessions', [
            'sessions' => $sessions,
        ]);
    }
}
