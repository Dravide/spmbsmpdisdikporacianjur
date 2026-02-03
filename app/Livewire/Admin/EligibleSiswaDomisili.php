<?php

namespace App\Livewire\Admin;

use App\Models\PesertaDidik;
use App\Models\SekolahMenengahPertama;
use App\Models\ZonaDomisili;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Eligible Siswa Domisili')]
class EligibleSiswaDomisili extends Component
{
    use WithPagination;

    public $search = '';

    // Detail Modal
    public $showDetailModal = false;
    public $selectedSiswa = null;
    public $eligibleSekolahList = [];

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function checkEligibility($siswaId)
    {
        $siswa = PesertaDidik::with('sekolah')->find($siswaId);

        if (!$siswa) {
            return;
        }

        $this->selectedSiswa = $siswa;

        // Find eligible schools based on domicile matching
        $query = ZonaDomisili::query()
            ->with('sekolah');

        // Match Kecamatan (required)
        if ($siswa->kecamatan) {
            $query->where('kecamatan', 'like', '%' . $siswa->kecamatan . '%');
        } else {
            // If no kecamatan, no schools are eligible
            $this->eligibleSekolahList = [];
            $this->showDetailModal = true;
            return;
        }

        // Match Desa (if available)
        if ($siswa->desa_kelurahan) {
            $query->where(function ($q) use ($siswa) {
                $q->where('desa', 'like', '%' . $siswa->desa_kelurahan . '%')
                    ->orWhere('desa', '');
            });
        }

        // Match RW (if available)
        if ($siswa->rw) {
            $query->where(function ($q) use ($siswa) {
                $q->where('rw', $siswa->rw)
                    ->orWhere('rw', '');
            });
        }

        // Match RT (if available)
        if ($siswa->rt) {
            $query->where(function ($q) use ($siswa) {
                $q->where('rt', $siswa->rt)
                    ->orWhere('rt', '');
            });
        }

        $zones = $query->get();

        // Group by school
        $groupedBySchool = $zones->groupBy('sekolah_id');

        $this->eligibleSekolahList = [];
        foreach ($groupedBySchool as $sekolahId => $zonas) {
            $sekolah = $zonas->first()->sekolah;
            if ($sekolah) {
                $this->eligibleSekolahList[] = [
                    'sekolah' => $sekolah->toArray(),
                    'matching_zones' => $zonas->map(function ($z) {
                        return [
                            'kecamatan' => $z->kecamatan,
                            'desa' => $z->desa,
                            'rw' => $z->rw,
                            'rt' => $z->rt,
                        ];
                    })->toArray(),
                ];
            }
        }

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedSiswa = null;
        $this->eligibleSekolahList = [];
    }

    public function render()
    {
        $pendaftarans = \App\Models\Pendaftaran::query()
            ->with(['pesertaDidik', 'pesertaDidik.sekolah'])
            ->when($this->search, function ($query) {
                $query->whereHas('pesertaDidik', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->whereHas('pesertaDidik', function ($q) {
                $q->whereNotNull('kecamatan')->where('kecamatan', '!=', '');
            })
            // Avoid duplicates if student has multiple registrations, typically latest or distinct
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count eligible schools for each student
        $pendaftarans->getCollection()->transform(function ($pendaftaran) {
            $siswa = $pendaftaran->pesertaDidik;
            $count = 0;
            if ($siswa && $siswa->kecamatan) {
                $query = ZonaDomisili::where('kecamatan', 'like', '%' . $siswa->kecamatan . '%');

                if ($siswa->desa_kelurahan) {
                    $query->where(function ($q) use ($siswa) {
                        $q->where('desa', 'like', '%' . $siswa->desa_kelurahan . '%')
                            ->orWhere('desa', '');
                    });
                }

                $count = $query->distinct('sekolah_id')->count('sekolah_id');
            }
            $pendaftaran->eligible_count = $count;
            return $pendaftaran;
        });

        return view('livewire.admin.eligible-siswa-domisili', [
            'pendaftarans' => $pendaftarans,
        ]);
    }
}
