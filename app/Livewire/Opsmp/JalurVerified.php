<?php

namespace App\Livewire\Opsmp;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('List Jalur Verified')]
class JalurVerified extends Component
{
    public $jalurList = [];
    public $selectedJalurId = null;
    public $selectedJalur = null;
    public $verifiedStudents = [];

    public function mount()
    {
        $this->loadJalurList();
    }

    public function loadJalurList()
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id;

        // Fetch Jalur with count of verified students for logged-in SMP
        $this->jalurList = JalurPendaftaran::withCount([
            'pendaftarans' => function ($query) use ($sekolahId) {
                $query->where('status', 'verified')
                    ->where('sekolah_menengah_pertama_id', $sekolahId);
            }
        ])->get();

        // If no jalur selected but list exists, default to none (or could default to first)
        // Leaving it null initially allows user to see summary cards first
    }

    public function selectJalur($id)
    {
        $this->selectedJalurId = $id;
        $this->selectedJalur = JalurPendaftaran::find($id);
        $this->loadVerifiedStudents();
    }

    public function loadVerifiedStudents()
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id;

        if ($this->selectedJalurId) {
            // Load students for specific jalur
            $this->verifiedStudents = Pendaftaran::with(['pesertaDidik.sekolah'])
                ->where('sekolah_menengah_pertama_id', $sekolahId)
                ->where('jalur_pendaftaran_id', $this->selectedJalurId)
                ->where('status', 'verified')
                ->latest('verified_at')
                ->get();
        } else {
            $this->verifiedStudents = [];
        }
    }

    public function render()
    {
        return view('livewire.opsmp.jalur-verified');
    }
}
