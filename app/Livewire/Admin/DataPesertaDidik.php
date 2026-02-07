<?php

namespace App\Livewire\Admin;

use App\Models\PesertaDidik;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Peserta Didik')]
class DataPesertaDidik extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public $file;

    public array $previewData = [];

    public bool $showPreview = false;

    public $importErrors = [];

    public $importedCount = 0;

    public $totalToImport = 0;

    public $isImporting = false;

    // Streaming Import Properties
    public $storedFilePath;

    public $lastProcessedLine = 0;

    // Edit/Create Properties
    public $isEditMode = false;

    public $editId = null;

    public $form = [
        'nama' => '',
        'nisn' => '',
        'nik' => '',
        'tempat_lahir' => '',
        'tanggal_lahir' => '',
        'jenis_kelamin' => '',
        'nama_ibu_kandung' => '',
        'sekolah_id' => '',
    ];

    // Filter Properties
    public $filterSekolah;

    public $filterKecamatan;

    public $filterDesa;

    public $filterJk;

    // Filter Data Lists
    public $sekolahList = [];

    public $kecamatanList = [];

    public $desaList = [];

    public function mount()
    {
        $this->loadFilterData();
    }

    public function loadFilterData()
    {
        // Load Sekolah List (Compact: ID => Nama)
        // Changed to SekolahDasar as per user request
        $this->sekolahList = \App\Models\SekolahDasar::orderBy('nama')->pluck('nama', 'sekolah_id')->toArray();

        // Load Kecamatan List from API or Database
        $this->loadKecamatanList();
    }

    public function loadKecamatanList()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://wilayah.id/api/districts/32.03.json');
            if ($response->successful()) {
                $this->kecamatanList = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->kecamatanList = [];
        }
    }

    public function updatedFilterKecamatan($value)
    {
        $this->filterDesa = '';
        $this->desaList = [];

        if ($value) {
            // Find code
            $selected = collect($this->kecamatanList)->firstWhere('name', $value);
            if ($selected) {
                $this->loadDesaList($selected['code']);
            }
        }
    }

    public function loadDesaList($code)
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("https://wilayah.id/api/villages/{$code}.json");
            if ($response->successful()) {
                $this->desaList = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            $this->desaList = [];
        }
    }

    public function resetFilters()
    {
        $this->reset(['filterSekolah', 'filterKecamatan', 'filterDesa', 'filterJk', 'desaList']);
        // Re-emit for Select2 clear
        $this->dispatch('filters-reset');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240', // 10MB max
        ]);

        // Store file permanently for the duration of the import
        $path = $this->file->store('imports');

        // Use Storage facade to get absolute path reliably
        $this->storedFilePath = Storage::path($path);

        $this->parseFilePreview();
    }

    protected function parseFilePreview()
    {
        if (! file_exists($this->storedFilePath)) {
            $this->addError('file', 'File tidak ditemukan di server.');

            return;
        }

        $handle = fopen($this->storedFilePath, 'r');
        if (! $handle) {
            $this->addError('file', 'Gagal membuka file.');

            return;
        }

        $header = null;
        $data = [];
        $count = 0;

        // Preview first 50 lines
        while (($row = fgetcsv($handle, 0, '|')) !== false && $count < 50) {
            if (count($row) < 5) {
                continue;
            }

            if (! $header) {
                // Remove BOM if present
                $row[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0]);

                $header = array_map(function ($h) {
                    return strtolower(trim($h, " \t\n\r\0\x0B\""));
                }, $row);

                continue;
            }

            $rowData = [];
            foreach ($header as $index => $key) {
                $value = isset($row[$index]) ? trim($row[$index], '"') : null;
                $rowData[$key] = ($value === '') ? null : $value;
            }

            // Relaxed validation: ensure we at least have a name
            if (! empty($rowData['nama'])) {
                $data[] = $rowData;
                $count++;
            }
        }
        fclose($handle);

        $this->previewData = $data;
        $this->showPreview = count($this->previewData) > 0;

        if (! $this->showPreview) {
            $this->addError('file', 'Format file tidak sesuai atau data kosong. Pastikan delimiter menggunakan Pipe (|) dan ada header kolom.');
        }

        // Count total lines for progress bar
        try {
            $this->totalToImport = count(file($this->storedFilePath)) - 1;
        } catch (\Exception $e) {
            $this->totalToImport = 1000;
        }
    }

    public function startImport()
    {
        if (! $this->storedFilePath || ! file_exists($this->storedFilePath)) {
            $this->dispatch('import-error', message: 'File import hilang. Silakan upload ulang.');

            return;
        }

        $this->importedCount = 0;
        $this->lastProcessedLine = 0;
        $this->importErrors = [];
        $this->isImporting = true;
    }

    public function processBatch($batchSize = 100)
    {
        if (! $this->isImporting || ! $this->storedFilePath) {
            $this->isImporting = false;

            return;
        }

        $handle = fopen($this->storedFilePath, 'r');
        $header = null;
        $currentLine = 0;
        $processedInBatch = 0;

        // Skip to where we left off
        while (($row = fgetcsv($handle, 0, '|')) !== false) {
            if (count($row) < 5) {
                continue;
            }

            if (! $header) {
                $row[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0]);
                $header = array_map(function ($h) {
                    return strtolower(trim($h, " \t\n\r\0\x0B\""));
                }, $row);

                continue;
            }

            if ($currentLine < $this->importedCount) {
                $currentLine++;

                continue;
            }

            if ($processedInBatch >= $batchSize) {
                break;
            }

            $rowData = [];
            foreach ($header as $index => $key) {
                $value = isset($row[$index]) ? trim($row[$index], '"') : null;
                $rowData[$key] = ($value === '') ? null : $value;
            }

            // Process Data
            try {
                if (! empty($rowData['peserta_didik_id']) && ! empty($rowData['nama'])) {
                    PesertaDidik::updateOrCreate(
                        ['peserta_didik_id' => $rowData['peserta_didik_id']],
                        [
                            'sekolah_id' => $rowData['sekolah_id'] ?? null,
                            'kode_wilayah' => $rowData['kode_wilayah'] ?? null,
                            'nama' => $rowData['nama'] ?? '-',
                            'tempat_lahir' => $rowData['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $rowData['tanggal_lahir'] ?? null,
                            'jenis_kelamin' => $rowData['jenis_kelamin'] ?? null,
                            'nik' => $rowData['nik'] ?? null,
                            'no_kk' => $rowData['no_kk'] ?? null,
                            'nisn' => $rowData['nisn'] ?? null,
                            'alamat_jalan' => $rowData['alamat_jalan'] ?? null,
                            'desa_kelurahan' => $rowData['desa_kelurahan'] ?? null,
                            'rt' => $rowData['rt'] ?? null,
                            'rw' => $rowData['rw'] ?? null,
                            'nama_dusun' => $rowData['nama_dusun'] ?? null,
                            'nama_ibu_kandung' => $rowData['nama_ibu_kandung'] ?? null,
                            'pekerjaan_ibu' => $rowData['pekerjaan_ibu'] ?? null,
                            'penghasilan_ibu' => $rowData['penghasilan_ibu'] ?? null,
                            'nama_ayah' => $rowData['nama_ayah'] ?? null,
                            'pekerjaan_ayah' => $rowData['pekerjaan_ayah'] ?? null,
                            'penghasilan_ayah' => $rowData['penghasilan_ayah'] ?? null,
                            'nama_wali' => $rowData['nama_wali'] ?? null,
                            'pekerjaan_wali' => $rowData['pekerjaan_wali'] ?? null,
                            'penghasilan_wali' => $rowData['penghasilan_wali'] ?? null,
                            'kebutuhan_khusus' => $rowData['kebutuhan_khusus'] ?? null,
                            'no_KIP' => $rowData['no_kip'] ?? null,
                            'no_pkh' => $rowData['no_pkh'] ?? null,
                            'lintang' => isset($rowData['lintang']) ? (float) $rowData['lintang'] : null,
                            'bujur' => isset($rowData['bujur']) ? (float) $rowData['bujur'] : null,
                            'flag_pip' => $rowData['flag_pip'] ?? null,
                            // Set default password from tanggal_lahir (Format: YYYYMMDD)
                            'password' => ! empty($rowData['tanggal_lahir'])
                                ? \Illuminate\Support\Facades\Hash::make(\Carbon\Carbon::parse($rowData['tanggal_lahir'])->format('Ymd'))
                                : null,
                        ]
                    );
                }
            } catch (\Exception $e) {
                // Silent fail
            }

            $currentLine++;
            $processedInBatch++;
        }

        fclose($handle);

        // Update processed count (data lines only)
        $this->importedCount += $processedInBatch;

        if ($this->totalToImport <= 0) {
            $this->totalToImport = 1;
        }
        $progress = ($this->importedCount / $this->totalToImport) * 100;

        if ($processedInBatch < $batchSize) {
            $this->isImporting = false;
            $this->reset(['file', 'previewData', 'showPreview', 'storedFilePath']);
            if (file_exists($this->storedFilePath)) {
                @unlink($this->storedFilePath);
            }
            $this->dispatch('import-success', message: "Import Selesai! {$this->importedCount} data diproses.");
        }

        return $progress;
    }

    public function cancelImport()
    {
        if ($this->storedFilePath && file_exists($this->storedFilePath)) {
            @unlink($this->storedFilePath);
        }
        $this->reset(['file', 'previewData', 'showPreview', 'storedFilePath']);
    }

    public function resetData()
    {
        PesertaDidik::truncate();
        $this->dispatch('import-success', message: 'Semua data peserta didik berhasil dihapus.');
    }

    public function delete($id)
    {
        $pd = PesertaDidik::find($id);
        if ($pd) {
            $pd->delete();
            $this->dispatch('import-success', message: 'Data siswa berhasil dihapus.');
        }
    }

    public function edit($id)
    {
        $pd = PesertaDidik::findOrFail($id);
        $this->editId = $id;
        $this->isEditMode = true;

        $tgl = $pd->tanggal_lahir;
        if ($tgl instanceof \Carbon\Carbon) {
            $tgl = $tgl->format('Y-m-d');
        } elseif (is_string($tgl)) {
            $tgl = substr($tgl, 0, 10);
        }

        $this->form = [
            'nama' => $pd->nama,
            'nisn' => $pd->nisn,
            'nik' => $pd->nik,
            'tempat_lahir' => $pd->tempat_lahir,
            'tanggal_lahir' => $tgl,
            'jenis_kelamin' => $pd->jenis_kelamin,
            'nama_ibu_kandung' => $pd->nama_ibu_kandung,
            'sekolah_id' => $pd->sekolah_id,
        ];
    }

    public function update()
    {
        $this->validate([
            'form.nama' => 'required',
            'form.nisn' => 'nullable',
        ]);

        $pd = PesertaDidik::findOrFail($this->editId);

        $data = $this->form;

        // Update password if date of birth changes or password is not set
        if (! empty($data['tanggal_lahir'])) {
            $dob = \Carbon\Carbon::parse($data['tanggal_lahir'])->format('Ymd');
            // Optionally only update password if explicitly requested, but usually DOB sync is intended
            // For now, let's keep password in sync with DOB for easy recovery
            $data['password'] = \Illuminate\Support\Facades\Hash::make($dob);
        }

        $pd->update($data);

        $this->reset(['isEditMode', 'editId', 'form']);
        $this->dispatch('import-success', message: 'Data siswa berhasil diperbarui.');
    }

    public function cancelEdit()
    {
        $this->reset(['isEditMode', 'editId', 'form']);
    }

    public function render()
    {
        $data = PesertaDidik::query()
            ->with('sekolah')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('nisn', 'like', "%{$this->search}%")
                        ->orWhere('nik', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterSekolah, fn ($q) => $q->where('sekolah_id', $this->filterSekolah))
            ->when($this->filterKecamatan, fn ($q) => $q->where('kecamatan', $this->filterKecamatan))
            ->when($this->filterDesa, fn ($q) => $q->where('desa_kelurahan', $this->filterDesa))
            ->when($this->filterJk, fn ($q) => $q->where('jenis_kelamin', $this->filterJk))
            ->paginate(15);

        return view('livewire.admin.data-peserta-didik', [
            'pesertaDidikList' => $data,
        ]);
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
