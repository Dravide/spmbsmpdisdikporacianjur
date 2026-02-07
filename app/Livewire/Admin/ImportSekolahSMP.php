<?php

namespace App\Livewire\Admin;

use App\Models\SekolahMenengahPertama;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Sekolah SMP')]
class ImportSekolahSMP extends Component
{
    use WithFileUploads;

    public $file;

    public array $previewData = [];

    public bool $showPreview = false;

    public bool $generateAccounts = false; // Default false for SMP

    // Streaming Import Properties
    public $storedFilePath;

    public $lastProcessedLine = 0;

    public $importedCount = 0;

    public $totalToImport = 0;

    public $isImporting = false;

    public $importErrors = [];

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|file|max:10240', // Relaxed validation
        ]);

        // Force local disk to ensure storage_path works
        $path = $this->file->store('imports', 'local');
        $this->storedFilePath = Storage::disk('local')->path($path);

        $this->parseFilePreview();
    }

    protected function parseFilePreview()
    {
        if (! file_exists($this->storedFilePath)) {
            $this->addError('file', 'File tidak ditemukan di server.');

            return;
        }

        $handle = fopen($this->storedFilePath, 'r');

        // Detect Delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiters = ['|', ';', ','];
        $delimiter = ','; // Default to comma if detection fails
        $found = false;

        foreach ($delimiters as $d) {
            // Check if delimiter appears at least once
            if (substr_count($firstLine, $d) >= 1) {
                $delimiter = $d;
                $found = true;
                break;
            }
        }

        $header = null;
        $data = [];
        $count = 0;

        // Preview first 50 lines
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false && $count < 50) {
            // Allow single column if it's just header or weird format, but usually we need more
            if (empty($row)) {
                continue;
            }

            if (! $header) {
                // Remove BOM
                $row[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $row[0]);
                $header = array_map(function ($h) {
                    return strtolower(trim($h, " \t\n\r\0\x0B\""));
                }, $row);

                continue;
            }

            $rowData = [];
            foreach ($header as $index => $key) {
                if (! empty($key) && isset($row[$index])) {
                    $rowData[$key] = trim($row[$index], '"');
                }
            }

            // We relax the check here to show at least something even if npsn is missing
            $data[] = $rowData;
            $count++;
        }
        fclose($handle);

        $this->previewData = $data;
        $this->showPreview = count($this->previewData) > 0;

        if (! $this->showPreview) {
            $this->addError('file', 'Data tidak terbaca. Pastikan format CSV benar (Delimiter: | atau ; atau ,).');
        }

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

    public function processBatch($batchSize = 50)
    {
        if (! $this->isImporting || ! $this->storedFilePath) {
            $this->isImporting = false;

            return;
        }

        $handle = fopen($this->storedFilePath, 'r');

        // Detect Delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiters = ['|', ';', ','];
        $delimiter = ',';
        foreach ($delimiters as $d) {
            if (substr_count($firstLine, $d) >= 1) {
                $delimiter = $d;
                break;
            }
        }

        $header = null;
        $currentLine = 0;
        $processedInBatch = 0;

        // Skip to where we left off
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty($row)) {
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
                if (! empty($key) && isset($row[$index])) {
                    $rowData[$key] = trim($row[$index], '"');
                }
            }

            // Process Data
            try {
                $statusSekolah = $rowData['status_sekolah'] ?? null;
                if ($statusSekolah === '1' || $statusSekolah === 1) {
                    $statusSekolah = 'Negeri';
                } elseif ($statusSekolah === '2' || $statusSekolah === 2) {
                    $statusSekolah = 'Swasta';
                }

                SekolahMenengahPertama::updateOrCreate(
                    ['sekolah_id' => $rowData['sekolah_id'] ?? $rowData['npsn']],
                    [
                        'npsn' => $rowData['npsn'],
                        'nama' => $rowData['nama'] ?? '',
                        'kode_wilayah' => $rowData['kode_wilayah'] ?? null,
                        'bentuk_pendidikan_id' => $rowData['bentuk_pendidikan_id'] ?? null,
                        'status_sekolah' => $statusSekolah,
                        'mode_spmb' => 'Semi Online', // Default for import
                        'alamat_jalan' => $rowData['alamat_jalan'] ?? null,
                        'desa_kelurahan' => $rowData['desa_kelurahan'] ?? null,
                        'rt' => $rowData['rt'] ?? null,
                        'rw' => $rowData['rw'] ?? null,
                        'lintang' => ! empty($rowData['lintang']) ? (float) $rowData['lintang'] : null,
                        'bujur' => ! empty($rowData['bujur']) ? (float) $rowData['bujur'] : null,
                    ]
                );

            } catch (\Exception $e) {
                // Silent fail
            }

            $currentLine++;
            $processedInBatch++;
        }

        fclose($handle);

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
            $this->dispatch('import-success', message: "Import Selesai! {$this->importedCount} sekolah SMP diproses.");
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

    public function render()
    {
        return view('livewire.admin.import-sekolah-smp');
    }
}
