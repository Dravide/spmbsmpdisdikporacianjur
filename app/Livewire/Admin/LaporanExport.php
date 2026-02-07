<?php

namespace App\Livewire\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\PesertaDidik;
use App\Models\SekolahMenengahPertama;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Laporan & Export')]
class LaporanExport extends Component
{
    public $reportType = 'rekapitulasi';

    public $filterSekolah = '';

    public $filterJalur = '';

    public $filterStatus = '';

    public $startDate = '';

    public $endDate = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getReportTypes()
    {
        return [
            'rekapitulasi' => [
                'name' => 'Rekapitulasi Pendaftaran',
                'description' => 'Ringkasan jumlah pendaftar per jalur dan status',
                'icon' => 'fi-rr-chart-pie',
            ],
            'pendaftar_jalur' => [
                'name' => 'Data Pendaftar per Jalur',
                'description' => 'Daftar lengkap pendaftar berdasarkan jalur pendaftaran',
                'icon' => 'fi-rr-road',
            ],
            'pendaftar_sekolah' => [
                'name' => 'Data Pendaftar per Sekolah',
                'description' => 'Daftar pendaftar berdasarkan sekolah tujuan',
                'icon' => 'fi-rr-school',
            ],
            'daya_tampung' => [
                'name' => 'Statistik Daya Tampung',
                'description' => 'Perbandingan daya tampung vs jumlah pendaftar',
                'icon' => 'fi-rr-stats',
            ],
            'verifikasi' => [
                'name' => 'Laporan Verifikasi Berkas',
                'description' => 'Status verifikasi berkas pendaftar',
                'icon' => 'fi-rr-document-signed',
            ],
            'daftar_ulang' => [
                'name' => 'Laporan Daftar Ulang',
                'description' => 'Data siswa yang sudah daftar ulang',
                'icon' => 'fi-rr-calendar-check',
            ],
        ];
    }

    public function exportExcel()
    {
        $filename = 'laporan_'.$this->reportType.'_'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(
            new \App\Exports\LaporanExport($this->reportType, [
                'sekolah' => $this->filterSekolah,
                'jalur' => $this->filterJalur,
                'status' => $this->filterStatus,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
            ]),
            $filename
        );
    }

    public function getPreviewData()
    {
        $query = Pendaftaran::with(['pesertaDidik', 'sekolah', 'jalur']);

        if ($this->filterSekolah) {
            $query->where('sekolah_menengah_pertama_id', $this->filterSekolah);
        }
        if ($this->filterJalur) {
            $query->where('jalur_pendaftaran_id', $this->filterJalur);
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->limit(10)->get();
    }

    public function getStatistics()
    {
        return [
            'total_pendaftar' => Pendaftaran::count(),
            'total_peserta_didik' => PesertaDidik::count(),
            'total_sekolah' => SekolahMenengahPertama::count(),
            'total_jalur' => JalurPendaftaran::count(),
            'by_status' => Pendaftaran::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_jalur' => Pendaftaran::join('jalur_pendaftarans', 'pendaftarans.jalur_pendaftaran_id', '=', 'jalur_pendaftarans.id')
                ->selectRaw('jalur_pendaftarans.nama as jalur, count(*) as count')
                ->groupBy('jalur_pendaftarans.nama')
                ->pluck('count', 'jalur')
                ->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.laporan-export', [
            'reportTypes' => $this->getReportTypes(),
            'sekolahs' => SekolahMenengahPertama::orderBy('nama')->get(),
            'jalurs' => JalurPendaftaran::orderBy('nama')->get(),
            'previewData' => $this->getPreviewData(),
            'statistics' => $this->getStatistics(),
        ]);
    }
}
