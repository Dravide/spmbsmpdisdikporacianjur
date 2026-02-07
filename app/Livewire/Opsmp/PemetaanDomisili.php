<?php

namespace App\Livewire\Opsmp;

use App\Models\SekolahMenengahPertama;
use App\Models\ZonaDomisili;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pemetaan Domisili')]
class PemetaanDomisili extends Component
{
    use \Livewire\WithFileUploads;

    public $sekolah;

    public $zonaList = [];

    // Form
    public $showFormModal = false;

    public $zonaId = null;

    public $kecamatan = '';

    public $desa = '';

    public $rw = '';

    public $rt = '';

    // Import
    public $showImportModal = false;

    public $importFile;

    // API Data
    public $kecamatanList = [];

    public $desaList = [];

    public function mount()
    {
        $this->sekolah = SekolahMenengahPertama::find(auth()->user()->sekolah_id);
        if (! $this->sekolah) {
            abort(403, 'Anda tidak terhubung dengan data sekolah.');
        }
        $this->loadZonaList();
    }

    public function loadZonaList()
    {
        $this->zonaList = ZonaDomisili::where('sekolah_id', $this->sekolah->sekolah_id)
            ->orderBy('kecamatan')
            ->orderBy('desa')
            ->get()
            ->toArray();
    }

    public function loadKecamatanList()
    {
        try {
            // Default Cianjur Code 32.03
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
        if ($zona && $zona->sekolah_id == $this->sekolah->sekolah_id) {
            $this->zonaId = $zona->id;
            $this->kecamatan = $zona->kecamatan;
            $this->desa = $zona->desa;
            $this->rw = $zona->rw;
            $this->rt = $zona->rt;
            $this->loadKecamatanList();

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
                'sekolah_id' => $this->sekolah->sekolah_id,
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
        $zona = ZonaDomisili::find($id);
        if ($zona && $zona->sekolah_id == $this->sekolah->sekolah_id) {
            $zona->delete();
            session()->flash('message', 'Zona berhasil dihapus.');
            $this->loadZonaList();
        }
    }

    public function resetAllZona()
    {
        $deleted = ZonaDomisili::where('sekolah_id', $this->sekolah->sekolah_id)->delete();
        $this->loadZonaList();
        $this->dispatch('resetCompleted');
    }

    // Import Logic
    public $previewData = [];

    public $importErrors = [];

    public $canImport = false;

    public $isImporting = false;

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle
        {
            public function headings(): array
            {
                return ['Kecamatan', 'Desa', 'RW', 'RT'];
            }

            public function array(): array
            {
                return [
                    ['Cianjur', 'Pamoyanan', '01', '01'],
                    ['Cianjur', 'Bojongherang', '02', '03'],
                ];
            }

            public function title(): string
            {
                return 'Template Zona';
            }
        }, 'template_zona_domisili.xlsx');
    }

    public function openImportModal()
    {
        $this->reset(['importFile', 'previewData', 'importErrors', 'canImport']);
        $this->showImportModal = true;
    }

    public function updatedImportFile()
    {
        $this->validate([
            'importFile' => 'required|max:5120|mimes:xlsx,xls',
        ]);

        $this->processImportPreview();
    }

    public function processImportPreview()
    {
        try {
            // Define anonymous class for Import
            $import = new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithHeadingRow
            {
                public function array(array $array) {}
            };

            $data = \Maatwebsite\Excel\Facades\Excel::toArray($import, $this->importFile);
            $rows = $data[0] ?? []; // Get first sheet

        } catch (\Exception $e) {
            $this->addError('importFile', 'Gagal membaca file Excel: '.$e->getMessage());

            return;
        }

        $processedRows = [];
        $validCount = 0;

        // Cianjur City Code: 3203 (Laravolt format often without dots)
        // Try both formats to be safe or stick to what we found (3203)
        $districts = \Laravolt\Indonesia\Models\District::where('city_code', '3203')->get();
        if ($districts->isEmpty()) {
            // Fallback to dotted format just in case
            $districts = \Laravolt\Indonesia\Models\District::where('city_code', '32.03')->get();
        }

        foreach ($rows as $row) {
            $kecamatanInput = trim($row['kecamatan'] ?? '');
            $desaInput = trim($row['desa'] ?? ($row['desakelurahan'] ?? '')); // Try 'desa' or 'desakelurahan'
            $rwInput = trim($row['rw'] ?? '');
            $rtInput = trim($row['rt'] ?? '');

            // Skip empty rows
            if (empty($kecamatanInput) && empty($desaInput)) {
                continue;
            }

            $status = 'Valid';
            $errorMsg = '';

            // Normalize Input: Remove 'Kecamatan' prefix if present
            $kecamatanName = preg_replace('/^kecamatan\s+/i', '', $kecamatanInput);

            // Normalize Input: Remove 'Desa'/'Kelurahan' prefix if present
            $desaName = preg_replace('/^(desa|kelurahan)\s+/i', '', $desaInput);

            // Validation
            if (empty($kecamatanInput) || empty($desaInput)) {
                $status = 'Invalid';
                $errorMsg = 'Kecamatan/Desa kosong';
            } else {
                // Check DB
                // 1. Check Kecamatan in Cianjur
                $district = $districts->first(function ($d) use ($kecamatanName) {
                    return strcasecmp($d->name, $kecamatanName) === 0;
                });

                if (! $district) {
                    $status = 'Invalid';
                    $errorMsg = 'Kecamatan tidak ditemukan di Cianjur';
                } else {
                    // 2. Check Desa in District
                    // Try precise match first, then case-insensitive collection search
                    $village = \Laravolt\Indonesia\Models\Village::where('district_code', $district->code)
                        ->where('name', 'like', $desaName)
                        ->first();

                    if (! $village) {
                        $allVillages = \Laravolt\Indonesia\Models\Village::where('district_code', $district->code)->get();
                        $village = $allVillages->first(function ($v) use ($desaName) {
                            return strcasecmp($v->name, $desaName) === 0;
                        });
                    }

                    if (! $village) {
                        $status = 'Invalid';
                        $errorMsg = 'Desa tidak ditemukan di Kecamatan '.$kecamatanInput;
                    }
                }
            }

            if ($status === 'Valid') {
                $validCount++;
            }

            $processedRows[] = [
                'kecamatan' => $kecamatanInput,
                'desa' => $desaInput,
                'rw' => $rwInput,
                'rt' => $rtInput,
                'status' => $status,
                'error' => $errorMsg,
            ];

            if (count($processedRows) > 100) {
                break;
            } // Limit preview
        }

        $this->previewData = $processedRows;
        $this->canImport = ($validCount > 0);
    }

    public function saveImport()
    {
        if (! $this->canImport || empty($this->previewData)) {
            return;
        }

        $this->isImporting = true;
        $count = 0;

        foreach ($this->previewData as $row) {
            if ($row['status'] === 'Valid') {
                ZonaDomisili::updateOrCreate(
                    [
                        'sekolah_id' => $this->sekolah->sekolah_id,
                        'kecamatan' => $row['kecamatan'],
                        'desa' => $row['desa'],
                        'rw' => $row['rw'],
                        'rt' => $row['rt'],
                    ],
                    [] // No other fields to update
                );
                $count++;
            }
        }

        $this->isImporting = false;
        $this->showImportModal = false;
        $this->loadZonaList();
        session()->flash('message', "Import berhasil! {$count} zona ditambahkan.");
    }

    public function closeImport()
    {
        $this->showImportModal = false;
        $this->reset(['importFile', 'previewData', 'canImport']);
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
        return view('livewire.opsmp.pemetaan-domisili');
    }
}
