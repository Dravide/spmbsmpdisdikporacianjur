<?php

namespace App\Livewire\Dashboard;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\SekolahMenengahPertama;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Operator SMP')]
class OpsmpDashboard extends Component
{
    public $sekolah;

    public function mount()
    {
        $this->sekolah = SekolahMenengahPertama::find(auth()->user()->sekolah_id);
    }

    public function getStatsProperty()
    {
        if (!$this->sekolah)
            return [];

        $totalDayaTampung = $this->sekolah->daya_tampung;
        $totalPendaftar = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)->count();
        $totalVerified = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
            ->where('status', 'verified')
            ->count();

        $percentageFilled = $totalDayaTampung > 0 ? round(($totalVerified / $totalDayaTampung) * 100, 1) : 0;

        return [
            'total_daya_tampung' => $totalDayaTampung,
            'total_pendaftar' => $totalPendaftar,
            'total_verified' => $totalVerified,
            'sisa_kuota' => max(0, $totalDayaTampung - $totalVerified), // Assuming quota is consumed by verified students
            'percentage_filled' => $percentageFilled
        ];
    }

    public function getJalurStatsProperty()
    {
        if (!$this->sekolah)
            return [];
        $totalDayaTampung = $this->sekolah->daya_tampung;
        $jalurs = JalurPendaftaran::where('aktif', true)->get();

        $stats = [];
        foreach ($jalurs as $jalur) {
            $kuotaSlot = (int) floor(($jalur->kuota / 100) * $totalDayaTampung);
            $terdaftar = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
                ->where('jalur_pendaftaran_id', $jalur->id)
                ->count();
            $verified = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
                ->where('jalur_pendaftaran_id', $jalur->id)
                ->where('status', 'verified')
                ->count();

            $stats[] = [
                'nama' => $jalur->nama,
                'kuota' => $kuotaSlot,
                'terdaftar' => $terdaftar,
                'verified' => $verified,
                'percentage' => $kuotaSlot > 0 ? round(($verified / $kuotaSlot) * 100, 1) : 0
            ];
        }
        return $stats;
    }

    public function getDailyRegistrationsProperty()
    {
        if (!$this->sekolah)
            return [];

        // Last 7 days
        $days = collect(range(6, 0))->map(function ($i) {
            return now()->subDays($i)->format('Y-m-d');
        });

        $counts = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        return $days->map(function ($date) use ($counts) {
            return [
                'date' => \Carbon\Carbon::parse($date)->format('d M'),
                'count' => $counts->get($date, 0)
            ];
        });
    }

    public function getRecentRegistrationsProperty()
    {
        if (!$this->sekolah)
            return [];

        return Pendaftaran::with(['pesertaDidik', 'jalur'])
            ->where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.opsmp-dashboard');
    }
}
