<?php

namespace App\Livewire\Opsmp;

use App\Models\Pendaftaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Pendaftaran')]
class DataPendaftaran extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedPendaftaran = null;

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function showDetail($id)
    {
        $this->selectedPendaftaran = Pendaftaran::with([
            'pesertaDidik.sekolah',
            'sekolah',
            'jalur',
            'berkas.berkas'
        ])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedPendaftaran = null;
    }

    public function render()
    {
        $user = Auth::user();
        // sekolah_id stores the SMP school ID for OPSMP users
        $sekolahId = $user->sekolah_id;

        $pendaftarans = Pendaftaran::query()
            ->with(['pesertaDidik.sekolah', 'sekolah', 'jalur'])
            ->where('sekolah_menengah_pertama_id', $sekolahId)
            ->when($this->search, function ($query) {
                $query->whereHas('pesertaDidik', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                })->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.opsmp.data-pendaftaran', [
            'pendaftarans' => $pendaftarans,
        ]);
    }
}
