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
        if ($this->pendaftaran->status == 'draft') {
            session()->flash('error', 'Tidak dapat memverifikasi berkas karena status pendaftaran masih Draft.');
            $this->closeModal();
            return;
        }

        $this->validate([
            'newStatus' => 'required|in:pending,approved,revision,rejected',
        ]);

        if ($this->selectedBerkas) {
            $this->selectedBerkas->status_berkas = $this->newStatus;
            $this->selectedBerkas->catatan_verifikasi = $this->catatan;
            $this->selectedBerkas->save();

            session()->flash('message', 'Status berkas berhasil diperbarui.');
            $this->checkRegistrationStatus(); // Check overall status
            $this->loadBerkas();
        }

        $this->closeModal();
    }

    public function quickApprove($id)
    {
        if ($this->pendaftaran->status == 'draft') {
            session()->flash('error', 'Tidak dapat memverifikasi berkas karena status pendaftaran masih Draft.');
            return;
        }

        $berkas = PendaftaranBerkas::find($id);
        if ($berkas) {
            $berkas->status_berkas = 'approved';
            $berkas->save();
            session()->flash('message', 'Berkas disetujui.');
            $this->checkRegistrationStatus(); // Check overall status
            $this->loadBerkas();
        }
    }

    public function approveAll()
    {
        if ($this->pendaftaran->status == 'draft') {
            session()->flash('error', 'Tidak dapat memverifikasi berkas karena status pendaftaran masih Draft.');
            return;
        }

        PendaftaranBerkas::where('pendaftaran_id', $this->pendaftaranId)
            ->update(['status_berkas' => 'approved']);

        session()->flash('message', 'Semua berkas disetujui.');
        $this->checkRegistrationStatus(); // Check overall status (will definitely be verified)
        $this->loadBerkas();
    }

    protected function checkRegistrationStatus()
    {
        $this->pendaftaran->refresh();

        // Check if there are any files NOT approved
        $hasPendingOrRejected = PendaftaranBerkas::where('pendaftaran_id', $this->pendaftaranId)
            ->where('status_berkas', '!=', 'approved')
            ->exists();

        if (!$hasPendingOrRejected) {
            $this->pendaftaran->status = 'verified';
            $this->pendaftaran->verified_at = now();
            $this->pendaftaran->verified_by = Auth::id();
            $this->pendaftaran->save();

            // Send Notification
            if ($this->pendaftaran->pesertaDidik) {
                $this->pendaftaran->pesertaDidik->notify(
                    \App\Notifications\StatusChangedNotification::verificationStatus($this->pendaftaran, 'verified')
                );
            }

            session()->flash('message', 'Semua berkas lengkap. Status pendaftaran otomatis berubah menjadi TERVERIFIKASI.');
        } else {
            // Optional: revert to submitted if not all approved? 
            // For now, let's just stick to upgrading to verified.
            // But if we want to be strict, if status was verified and now we reject one, should we revert?
            // The user request was "makan jadi vrifired juga" (then become verified too).
            // It doesn't explicitly ask for downgrading, but it's safer to keep it consistent.

            if ($this->pendaftaran->status == 'verified') {
                // Downgrade back to submitted if not all approved anymore
                $this->pendaftaran->status = 'submitted';
                $this->pendaftaran->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.opsmp.verval-berkas-detail');
    }
}
