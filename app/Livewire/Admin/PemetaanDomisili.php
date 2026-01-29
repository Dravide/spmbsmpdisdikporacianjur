<?php

namespace App\Livewire\Admin;

use App\Models\SekolahMenengahPertama;
use App\Models\ZonaDomisili;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pemetaan Domisili')]
class PemetaanDomisili extends Component
{
    use WithPagination;

    public $search = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedSekolah = null;
    public $zonaList = [];

    // Form
    public $showFormModal = false;
    public $zonaId = null;
    public $kecamatan = '';
    public $desa = '';
    public $rw = '';
    public $rt = '';

    // API Data
    public $kecamatanList = [];
    public $desaList = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openDetail($sekolahId)
    {
        $this->selectedSekolah = SekolahMenengahPertama::find($sekolahId);
        $this->loadZonaList();
        $this->showDetailModal = true;
    }

    public function loadZonaList()
    {
        if ($this->selectedSekolah) {
            $this->zonaList = ZonaDomisili::where('sekolah_id', $this->selectedSekolah->sekolah_id)
                ->orderBy('kecamatan')
                ->orderBy('desa')
                ->get()
                ->toArray();
        }
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedSekolah = null;
        $this->zonaList = [];
    }

    public function loadKecamatanList()
    {
        try {
            $response = Http::timeout(10)->get('https://wilayah.id/api/districts/32.03.json');
            if ($response->successful()) {
                $this->kecamatanList = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->kecamatanList = [];
        }
    }

    public function updatedKecamatan($value)
    {
        $this->desa = '';
        $this->desaList = [];

        if ($value) {
            // Find kecamatan code from name
            $kecamatanData = collect($this->kecamatanList)->firstWhere('name', $value);
            if ($kecamatanData) {
                $this->loadDesaList($kecamatanData['code']);
            }
        }
    }

    public function loadDesaList($kecamatanCode)
    {
        try {
            $response = Http::timeout(10)->get("https://wilayah.id/api/villages/{$kecamatanCode}.json");
            if ($response->successful()) {
                $this->desaList = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->desaList = [];
        }
    }

    public function createZona()
    {
        $this->resetForm();
        $this->loadKecamatanList();
        $this->showFormModal = true;
    }

    public function editZona($id)
    {
        $zona = ZonaDomisili::find($id);
        if ($zona) {
            $this->zonaId = $zona->id;
            $this->kecamatan = $zona->kecamatan;
            $this->desa = $zona->desa;
            $this->rw = $zona->rw;
            $this->rt = $zona->rt;
            $this->loadKecamatanList();

            // Load desa list for selected kecamatan
            $kecamatanData = collect($this->kecamatanList)->firstWhere('name', $zona->kecamatan);
            if ($kecamatanData) {
                $this->loadDesaList($kecamatanData['code']);
            }

            $this->showFormModal = true;
        }
    }

    public function saveZona()
    {
        $this->validate([
            'kecamatan' => 'required',
            'desa' => 'required',
            'rw' => 'required',
            'rt' => 'required',
        ], [
            'kecamatan.required' => 'Kecamatan wajib dipilih',
            'desa.required' => 'Desa wajib dipilih',
            'rw.required' => 'RW wajib diisi',
            'rt.required' => 'RT wajib diisi',
        ]);

        ZonaDomisili::updateOrCreate(
            ['id' => $this->zonaId],
            [
                'sekolah_id' => $this->selectedSekolah->sekolah_id,
                'kecamatan' => $this->kecamatan,
                'desa' => $this->desa,
                'rw' => $this->rw,
                'rt' => $this->rt,
            ]
        );

        session()->flash('message', $this->zonaId ? 'Zona berhasil diperbarui.' : 'Zona berhasil ditambahkan.');
        $this->showFormModal = false;
        $this->resetForm();
        $this->loadZonaList();
    }

    public function deleteZona($id)
    {
        ZonaDomisili::destroy($id);
        session()->flash('message', 'Zona berhasil dihapus.');
        $this->loadZonaList();
    }

    public function resetForm()
    {
        $this->zonaId = null;
        $this->kecamatan = '';
        $this->desa = '';
        $this->rw = '';
        $this->rt = '';
        $this->kecamatanList = [];
        $this->desaList = [];
    }

    public function render()
    {
        $sekolahs = SekolahMenengahPertama::query()
            ->where('mode_spmb', 'Full Online')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('npsn', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('zonaDomisili')
            ->orderBy('nama')
            ->paginate(15);

        return view('livewire.admin.pemetaan-domisili', [
            'sekolahs' => $sekolahs,
        ]);
    }
}
