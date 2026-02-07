<?php

namespace App\Livewire\Opsmp;

use App\Models\PendaftaranBerkas;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Verval Berkas')]
class VervalBerkas extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    // Modal
    public $showModal = false;

    public $selectedBerkas = null;

    public $newStatus = '';

    public $catatan = '';

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function openModal($id)
    {
        $this->selectedBerkas = PendaftaranBerkas::with(['pendaftaran.pesertaDidik', 'berkas'])->find($id);
        if ($this->selectedBerkas) {
            $this->newStatus = $this->selectedBerkas->status_berkas;
            $this->catatan = $this->selectedBerkas->catatan_verifikasi ?? '';
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBerkas = null;
        $this->newStatus = '';
        $this->catatan = '';
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:pending,approved,revision,rejected',
        ]);

        if ($this->selectedBerkas) {
            $this->selectedBerkas->status_berkas = $this->newStatus;
            $this->selectedBerkas->catatan_verifikasi = $this->catatan;
            $this->selectedBerkas->save();

            session()->flash('message', 'Status berkas berhasil diperbarui.');
        }

        $this->closeModal();
    }

    public function quickApprove($id)
    {
        $berkas = PendaftaranBerkas::with(['pendaftaran.pesertaDidik'])->find($id);
        if ($berkas) {
            $berkas->status_berkas = 'approved';
            $berkas->save();
            session()->flash('message', 'Berkas disetujui.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        // sekolah_id stores the SMP school ID for OPSMP users
        $sekolahId = $user->sekolah_id;

        // Get all berkas from pendaftarans for this school
        $berkasList = PendaftaranBerkas::query()
            ->with(['pendaftaran.pesertaDidik', 'berkas'])
            ->whereHas('pendaftaran', function ($q) use ($sekolahId) {
                $q->where('sekolah_menengah_pertama_id', $sekolahId);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('pendaftaran.pesertaDidik', function ($q) {
                    $q->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nisn', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status_berkas', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.opsmp.verval-berkas', [
            'berkasList' => $berkasList,
        ]);
    }
}
