<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Siswa - SPMB Disdikpora')]
class StudentDashboard extends Component
{
    public function logout()
    {
        Auth::guard('siswa')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.dashboard.student-dashboard', [
            'user' => Auth::guard('siswa')->user(),
        ]);
    }
}
