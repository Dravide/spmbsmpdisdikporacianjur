<?php

namespace App\Livewire\Dashboard;

use App\Models\LoginSession;
use App\Models\RoleSetting;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Admin')]
class AdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.admin-dashboard', [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'activeSessions' => LoginSession::count(),
            'usersByRole' => User::selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
            'roleSettings' => RoleSetting::all(),
            'recentLogins' => LoginSession::with('user')
                ->orderBy('last_activity', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }
}
