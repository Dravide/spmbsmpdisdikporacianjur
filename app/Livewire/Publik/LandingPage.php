<?php

namespace App\Livewire\Publik;

use App\Models\Jadwal;
use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\SekolahMenengahPertama;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Selamat Datang')]
class LandingPage extends Component
{
    public function getStatistics()
    {
        return [
            'total_pendaftar' => Pendaftaran::count(),
            'total_sekolah' => SekolahMenengahPertama::count(),
            'total_jalur' => JalurPendaftaran::count(),
            'total_diterima' => Pendaftaran::where('status', 'diterima')->count(),
        ];
    }

    public function getJadwals()
    {
        return Jadwal::orderBy('tanggal_mulai')->get();
    }

    public function getSchools()
    {
        return SekolahMenengahPertama::withCount([
            'pendaftarans' => function ($q) {
                $q->where('status', '!=', 'ditolak');
            }
        ])
            ->orderByDesc('pendaftarans_count')
            ->limit(6)
            ->get();
    }

    public function getFaqs()
    {
        return [
            [
                'question' => 'Apa saja syarat untuk mendaftar SPMB?',
                'answer' => 'Calon peserta didik harus: 1) Lulusan SD/MI/sederajat tahun ini atau tahun sebelumnya, 2) Berusia maksimal 15 tahun pada tanggal 1 Juli tahun berjalan, 3) Memiliki NISN yang terdaftar di Dapodik, 4) Memiliki dokumen yang diperlukan (ijazah/SKL, KK, akta kelahiran).'
            ],
            [
                'question' => 'Bagaimana cara mendaftar secara online?',
                'answer' => 'Kunjungi halaman pendaftaran, klik "Daftar Sekarang", isi data diri lengkap sesuai dokumen, upload berkas yang diperlukan, pilih sekolah tujuan dan jalur pendaftaran, lalu kirim formulir pendaftaran Anda.'
            ],
            [
                'question' => 'Jalur pendaftaran apa saja yang tersedia?',
                'answer' => 'Tersedia beberapa jalur pendaftaran: 1) Jalur Zonasi berdasarkan domisili, 2) Jalur Afirmasi untuk pemegang KIP/PKH, 3) Jalur Prestasi untuk siswa berprestasi, 4) Jalur Perpindahan Orang Tua.'
            ],
            [
                'question' => 'Kapan pengumuman hasil seleksi?',
                'answer' => 'Pengumuman hasil seleksi akan diumumkan sesuai jadwal yang tertera di timeline. Hasil dapat dilihat langsung di portal siswa dengan login menggunakan NISN.'
            ],
            [
                'question' => 'Bagaimana jika berkas ditolak?',
                'answer' => 'Jika berkas ditolak, Anda akan mendapat notifikasi beserta alasan penolakan. Silakan perbaiki berkas sesuai catatan verifikator dan upload ulang sebelum batas waktu yang ditentukan.'
            ],
        ];
    }

    public function getCountdownTarget()
    {
        // Get the next important date from jadwal
        $nextJadwal = Jadwal::where('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->first();

        if ($nextJadwal) {
            return [
                'date' => $nextJadwal->tanggal_mulai,
                'label' => $nextJadwal->nama,
            ];
        }

        return null;
    }

    public function render()
    {
        return view('livewire.publik.landing-page', [
            'statistics' => $this->getStatistics(),
            'jadwals' => $this->getJadwals(),
            'schools' => $this->getSchools(),
            'faqs' => $this->getFaqs(),
            'countdown' => $this->getCountdownTarget(),
        ]);
    }
}
