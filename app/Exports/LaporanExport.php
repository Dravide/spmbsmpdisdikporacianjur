<?php

namespace App\Exports;

use App\Models\Pendaftaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $reportType;

    protected $filters;

    public function __construct($reportType, $filters = [])
    {
        $this->reportType = $reportType;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pendaftaran::with(['pesertaDidik', 'sekolah', 'jalur']);

        if (! empty($this->filters['sekolah'])) {
            $query->where('sekolah_menengah_pertama_id', $this->filters['sekolah']);
        }
        if (! empty($this->filters['jalur'])) {
            $query->where('jalur_pendaftaran_id', $this->filters['jalur']);
        }
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (! empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (! empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'NISN',
            'Nama Peserta Didik',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Sekolah Asal',
            'Sekolah Tujuan',
            'Jalur',
            'Status',
            'Jarak (meter)',
            'Tanggal Daftar',
        ];
    }

    public function map($row): array
    {
        return [
            $row->nomor_pendaftaran ?? '-',
            $row->pesertaDidik->nisn ?? '-',
            $row->pesertaDidik->nama ?? '-',
            $row->pesertaDidik->jenis_kelamin ?? '-',
            $row->pesertaDidik->tempat_lahir ?? '-',
            $row->pesertaDidik->tanggal_lahir?->format('d/m/Y') ?? '-',
            $row->pesertaDidik->sekolah->nama ?? '-',
            $row->sekolah->nama ?? '-',
            $row->jalur->nama ?? '-',
            ucfirst($row->status ?? '-'),
            $row->jarak_meter ? number_format($row->jarak_meter, 0) : '-',
            $row->tanggal_daftar?->format('d/m/Y') ?? $row->created_at?->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667eea'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        $titles = [
            'rekapitulasi' => 'Rekapitulasi',
            'pendaftar_jalur' => 'Per Jalur',
            'pendaftar_sekolah' => 'Per Sekolah',
            'daya_tampung' => 'Daya Tampung',
            'verifikasi' => 'Verifikasi',
            'daftar_ulang' => 'Daftar Ulang',
        ];

        return $titles[$this->reportType] ?? 'Laporan';
    }
}
