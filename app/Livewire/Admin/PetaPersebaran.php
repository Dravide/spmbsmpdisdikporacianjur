<?php

namespace App\Livewire\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\SekolahMenengahPertama;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Peta Persebaran')]
class PetaPersebaran extends Component
{
    public $filterSekolah = '';

    public $filterJalur = '';

    public $filterStatus = '';

    public $filterKecamatan = '';

    public $mapMode = 'markers'; // markers or heatmap

    public function updated($property)
    {
        // Dispatch event to update map markers when any filter changes
        $this->dispatch('markers-updated', markers: $this->getMarkers(), total: count($this->getMarkers()));
    }

    public function getMarkers()
    {
        $query = Pendaftaran::with(['pesertaDidik', 'sekolah', 'jalur'])
            ->whereHas('pesertaDidik', function ($q) {
                $q->whereNotNull('lintang')
                    ->whereNotNull('bujur')
                    ->where('lintang', '!=', 0)
                    ->where('bujur', '!=', 0);
            });

        if ($this->filterSekolah) {
            $query->where('sekolah_menengah_pertama_id', $this->filterSekolah);
        }
        if ($this->filterJalur) {
            $query->where('jalur_pendaftaran_id', $this->filterJalur);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterKecamatan) {
            $query->whereHas('pesertaDidik', function ($q) {
                $q->where('kecamatan', 'like', '%'.$this->filterKecamatan.'%');
            });
        }

        return $query->get()->map(function ($p) {
            return [
                'lat' => $p->pesertaDidik->lintang,
                'lng' => $p->pesertaDidik->bujur,
                'nama' => $p->pesertaDidik->nama,
                'nisn' => $p->pesertaDidik->nisn,
                'sekolah' => $p->sekolah->nama ?? '-',
                'jalur' => $p->jalur->nama ?? '-',
                'status' => $p->status,
                'kecamatan' => $p->pesertaDidik->kecamatan ?? '-',
            ];
        })->toArray();
    }

    public function getKecamatanStats()
    {
        return Pendaftaran::join('peserta_didiks', 'pendaftarans.peserta_didik_id', '=', 'peserta_didiks.id')
            ->selectRaw('peserta_didiks.kecamatan, count(*) as count')
            ->whereNotNull('peserta_didiks.kecamatan')
            ->groupBy('peserta_didiks.kecamatan')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'kecamatan')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.peta-persebaran', [
            'markers' => $this->getMarkers(),
            'sekolahs' => SekolahMenengahPertama::orderBy('nama')->get(),
            'jalurs' => JalurPendaftaran::orderBy('nama')->get(),
            'kecamatanStats' => $this->getKecamatanStats(),
            'totalMarkers' => count($this->getMarkers()),
        ]);
    }
}
