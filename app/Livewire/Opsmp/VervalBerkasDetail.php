<?php

namespace App\Livewire\Opsmp;

use App\Models\Pendaftaran;
use App\Models\PendaftaranBerkas;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Verval Berkas Siswa')]
class VervalBerkasDetail extends Component
{
    public $pendaftaranId;
    public $pendaftaran;
    public $berkasList = [];

    // Modal
    public $showModal = false;
    public $selectedBerkas = null;
    public $newStatus = '';
    public $catatan = '';

    public function mount($id)
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id;

        $this->pendaftaranId = $id;
        $this->pendaftaran = Pendaftaran::with(['pesertaDidik.sekolah', 'sekolah', 'jalur'])
            ->where('sekolah_menengah_pertama_id', $sekolahId)
            ->where('id', $id)
            ->firstOrFail();

        $this->loadBerkas();
    }

    public function loadBerkas()
    {
        $this->berkasList = PendaftaranBerkas::with('berkas')
            ->where('pendaftaran_id', $this->pendaftaranId)
            ->get();
    }

    public function openModal($id)
    {
        $this->selectedBerkas = PendaftaranBerkas::with('berkas')->find($id);
        if ($this->selectedBerkas) {
            $this->newStatus = $this->selectedBerkas->status_berkas ?? 'pending';
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
            $this->loadBerkas();
        }

        $this->closeModal();
    }

    public function quickApprove($id)
    {
        $berkas = PendaftaranBerkas::find($id);
        if ($berkas) {
            $berkas->status_berkas = 'approved';
            $berkas->save();
            session()->flash('message', 'Berkas disetujui.');
            $this->loadBerkas();
        }
    }

    public function approveAll()
    {
        PendaftaranBerkas::where('pendaftaran_id', $this->pendaftaranId)
            ->update(['status_berkas' => 'approved']);
        session()->flash('message', 'Semua berkas disetujui.');
        $this->loadBerkas();
    }

    public function render()
    {
        return view('livewire.opsmp.verval-berkas-detail');
    }
}
