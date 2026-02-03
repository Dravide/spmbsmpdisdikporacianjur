<?php

namespace App\Livewire\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\SekolahMenengahPertama;
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
    public $filterJalur = '';
    public $filterSekolah = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedPendaftaran = null;

    // Status Update Modal
    public $showStatusModal = false;
    public $statusPendaftaranId = null;
    public $newStatus = '';
    public $catatan = '';

    protected $queryString = ['search', 'filterStatus', 'filterJalur', 'filterSekolah'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterJalur()
    {
        $this->resetPage();
    }

    public function updatingFilterSekolah()
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

    public function openStatusModal($id)
    {
        $pendaftaran = Pendaftaran::find($id);
        if ($pendaftaran) {
            $this->statusPendaftaranId = $id;
            $this->newStatus = $pendaftaran->status;
            $this->catatan = $pendaftaran->catatan ?? '';
            $this->showStatusModal = true;
        }
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->statusPendaftaranId = null;
        $this->newStatus = '';
        $this->catatan = '';
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:draft,submitted,verified,accepted,rejected',
        ]);

        $pendaftaran = Pendaftaran::find($this->statusPendaftaranId);
        if ($pendaftaran) {
            $pendaftaran->status = $this->newStatus;
            $pendaftaran->catatan = $this->catatan;
            $pendaftaran->save();

            // Reset status berkas jika kembali ke draft atau submitted
            if (in_array($this->newStatus, ['draft', 'submitted'])) {
                $pendaftaran->berkas()->update(['status_berkas' => 'pending']);
            }

            session()->flash('message', 'Status pendaftaran berhasil diperbarui.');

            // Send Notification based on status
            if ($pendaftaran->pesertaDidik && in_array($this->newStatus, ['verified', 'rejected', 'accepted', 'diterima', 'ditolak'])) {
                if ($this->newStatus == 'verified' || $this->newStatus == 'rejected') {
                    $pendaftaran->pesertaDidik->notify(
                        \App\Notifications\StatusChangedNotification::verificationStatus($pendaftaran, $this->newStatus, $this->catatan)
                    );
                } elseif ($this->newStatus == 'accepted' || $this->newStatus == 'diterima' || $this->newStatus == 'ditolak') {
                    // Normalize status for announcement
                    $status = ($this->newStatus == 'accepted') ? 'diterima' : $this->newStatus;
                    $pendaftaran->pesertaDidik->notify(
                        \App\Notifications\StatusChangedNotification::announcement($status)
                    );
                }
            }
        }

        $this->closeStatusModal();
    }

    public function delete($id)
    {
        $pendaftaran = Pendaftaran::find($id);

        if ($pendaftaran) {
            $pendaftaran->delete();
            session()->flash('message', 'Data pendaftaran berhasil dihapus.');
        }
    }

    public function render()
    {
        $pendaftarans = Pendaftaran::query()
            ->with(['pesertaDidik.sekolah', 'sekolah', 'jalur'])
            ->when($this->search, function ($query) {
                $query->whereHas('pesertaDidik', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                })->orWhere('nomor_pendaftaran', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterJalur, function ($query) {
                $query->where('jalur_pendaftaran_id', $this->filterJalur);
            })
            ->when($this->filterSekolah, function ($query) {
                $query->where('sekolah_menengah_pertama_id', $this->filterSekolah);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $jalurList = JalurPendaftaran::where('aktif', true)->get();
        $sekolahList = SekolahMenengahPertama::orderBy('nama')->get();

        return view('livewire.admin.data-pendaftaran', [
            'pendaftarans' => $pendaftarans,
            'jalurList' => $jalurList,
            'sekolahList' => $sekolahList,
        ]);
    }
}
