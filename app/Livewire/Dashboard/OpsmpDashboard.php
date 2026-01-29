<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Operator SMP')]
class OpsmpDashboard extends Component
{
    public $sekolah;

    public function mount()
    {
        $this->sekolah = auth()->user()->sekolah;
    }

    public function render()
    {
        return view('livewire.dashboard.opsmp-dashboard');
    }
}
