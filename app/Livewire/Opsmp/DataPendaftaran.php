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

    public $filterJalur = '';

    public $filterSekolah = ''; // New Filter

    public $startDate = '';

    public $endDate = '';

    // Detail Modal
    public $showDetailModal = false;

    public $selectedPendaftaran = null;

    protected $queryString = ['search', 'filterStatus', 'filterJalur', 'filterSekolah', 'startDate', 'endDate'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterJalur', 'filterSekolah', 'startDate', 'endDate', 'search']);
        $this->resetPage();
    }

    public function showDetail($id)
    {
        $this->selectedPendaftaran = Pendaftaran::with([
            'pesertaDidik.sekolah',
            'sekolah',
            'jalur',
            'berkas.berkas',
        ])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedPendaftaran = null;
    }

    public function delete($id)
    {
        $pendaftaran = Pendaftaran::find($id);
        $user = Auth::user();

        if ($pendaftaran && $pendaftaran->sekolah_menengah_pertama_id == $user->sekolah_id) {
            foreach ($pendaftaran->berkas as $berkas) {
                if ($berkas->file_path && \Storage::disk('public')->exists($berkas->file_path)) {
                    \Storage::disk('public')->delete($berkas->file_path);
                }
                $berkas->delete();
            }

            $pendaftaran->delete();
            session()->flash('message', 'Data pendaftaran berhasil dihapus.');
        } else {
            session()->flash('error', 'Data pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        // sekolah_id stores the SMP school ID for OPSMP users
        $sekolahId = $user->sekolah_id;

        $jalurList = \App\Models\JalurPendaftaran::all();

        // Get list of Sekolah Dasar present in current applications to this school (optimized)
        $sekolahDasarList = \App\Models\SekolahDasar::whereHas('pesertaDidik.pendaftaran', function ($q) use ($sekolahId) {
            $q->where('sekolah_menengah_pertama_id', $sekolahId);
        })->orderBy('nama')->get();

        $pendaftarans = Pendaftaran::query()
            ->with(['pesertaDidik.sekolah', 'sekolah', 'jalur'])
            ->where('sekolah_menengah_pertama_id', $sekolahId)
            ->when($this->search, function ($query) {
                $query->whereHas('pesertaDidik', function ($q) {
                    $q->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nisn', 'like', '%'.$this->search.'%');
                })->orWhere('nomor_pendaftaran', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterJalur, function ($query) {
                $query->where('jalur_pendaftaran_id', $this->filterJalur);
            })
            ->when($this->filterSekolah, function ($query) {
                $query->whereHas('pesertaDidik', function ($q) {
                    $q->where('sekolah_id', $this->filterSekolah);
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('tanggal_daftar', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('tanggal_daftar', '<=', $this->endDate);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.opsmp.data-pendaftaran', [
            'pendaftarans' => $pendaftarans,
            'jalurList' => $jalurList,
            'sekolahDasarList' => $sekolahDasarList,
        ]);
    }
}
