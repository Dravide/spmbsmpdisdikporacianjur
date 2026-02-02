<?php

namespace App\Livewire\Student;

use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Announcement extends Component
{
    #[Layout('layouts.app')]
    #[Title('Pengumuman Hasil Seleksi')]
    public function render()
    {
        $user = Auth::user();
        $siswa = ($user instanceof \App\Models\PesertaDidik) ? $user : ($user->pesertaDidik ?? null);

        $pengumuman = null;
        if ($siswa) {
            $pengumuman = Pengumuman::where('peserta_didik_id', $siswa->id)
                ->with(['sekolah', 'jalur'])
                ->first();
        }

        $scheduleOpen = \App\Models\Jadwal::isOpen('pengumuman');
        $scheduleStartDate = null;

        if (!$scheduleOpen) {
            $jadwal = \App\Models\Jadwal::where('keyword', 'pengumuman')->first();
            if ($jadwal && $jadwal->aktif && now()->lessThan($jadwal->tanggal_mulai)) {
                $scheduleStartDate = $jadwal->tanggal_mulai->toISOString();
            }
        }

        return view('livewire.student.announcement', [
            'pengumuman' => $pengumuman,
            'siswa' => $siswa,
            'isScheduleOpen' => $scheduleOpen,
            'scheduleMessage' => \App\Models\Jadwal::getMessage('pengumuman'),
            'scheduleStartDate' => $scheduleStartDate,
        ]);
    }
}
