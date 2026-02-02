<?php

namespace App\Livewire\Opsmp;

use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pengumuman Seleksi')]
class ListPengumuman extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJalur = '';
    public $filterStatus = '';
    public $dateStart = '';
    public $dateEnd = '';



    public function getStatisticsProperty()
    {
        $user = Auth::user();
        if (!$user)
            return [];

        $baseQuery = Pengumuman::where('sekolah_menengah_pertama_id', $user->sekolah_id);

        return [
            'total' => (clone $baseQuery)->count(),
            'lulus' => (clone $baseQuery)->where('status', 'lulus')->count(),
            'tidak_lulus' => (clone $baseQuery)->where('status', 'tidak_lulus')->count(),
        ];
    }

    public function resetFilter()
    {
        $this->reset(['search', 'filterJalur', 'filterStatus', 'dateStart', 'dateEnd']);
    }

    public function resetData()
    {
        $user = Auth::user();
        if (!$user)
            return;

        Pengumuman::where('sekolah_menengah_pertama_id', $user->sekolah_id)->delete();

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data pengumuman berhasil direset.',
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id;

        $query = Pengumuman::with(['pesertaDidik', 'jalur', 'pendaftaran'])
            ->where('sekolah_menengah_pertama_id', $sekolahId);

        if ($this->search) {
            $query->whereHas('pesertaDidik', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterJalur) {
            $query->where('jalur_pendaftaran_id', $this->filterJalur);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->dateStart) {
            $query->whereDate('created_at', '>=', $this->dateStart);
        }

        if ($this->dateEnd) {
            $query->whereDate('created_at', '<=', $this->dateEnd);
        }

        $pengumumans = $query->latest()->paginate(10);

        $jalurList = \App\Models\JalurPendaftaran::all();

        return view('livewire.opsmp.list-pengumuman', [
            'pengumumans' => $pengumumans,
            'jalurList' => $jalurList,
            'statistics' => $this->statistics
        ]);
    }
}
