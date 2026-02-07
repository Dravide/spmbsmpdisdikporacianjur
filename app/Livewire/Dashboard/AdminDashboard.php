<?php

namespace App\Livewire\Dashboard;

use App\Models\JalurPendaftaran;
use App\Models\LoginSession;
use App\Models\Pendaftaran;
use App\Models\PesertaDidik;
use App\Models\RoleSetting;
use App\Models\SekolahMenengahPertama;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Admin')]
class AdminDashboard extends Component
{
    public function getRegistrationStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $totalToday = Pendaftaran::whereDate('created_at', $today)->count();
        $totalYesterday = Pendaftaran::whereDate('created_at', $yesterday)->count();

        $percentChange = $totalYesterday > 0
            ? round((($totalToday - $totalYesterday) / $totalYesterday) * 100, 1)
            : ($totalToday > 0 ? 100 : 0);

        return [
            'total' => Pendaftaran::count(),
            'today' => $totalToday,
            'yesterday' => $totalYesterday,
            'percentChange' => $percentChange,
        ];
    }

    public function getStatusBreakdown()
    {
        return Pendaftaran::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getDailyRegistrations($days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Pendaftaran::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => $date->format('d M'),
                'count' => $count,
            ];
        }

        return $data;
    }

    public function getRegistrationByJalur()
    {
        return Pendaftaran::join('jalur_pendaftarans', 'pendaftarans.jalur_pendaftaran_id', '=', 'jalur_pendaftarans.id')
            ->selectRaw('jalur_pendaftarans.nama as jalur, count(*) as count')
            ->groupBy('jalur_pendaftarans.nama')
            ->pluck('count', 'jalur')
            ->toArray();
    }

    public function getTopSchools($limit = 10)
    {
        return Pendaftaran::join('sekolah_menengah_pertamas', 'pendaftarans.sekolah_menengah_pertama_id', '=', 'sekolah_menengah_pertamas.sekolah_id')
            ->selectRaw('sekolah_menengah_pertamas.nama as sekolah, count(*) as count')
            ->groupBy('sekolah_menengah_pertamas.nama')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'sekolah')
            ->toArray();
    }

    public function getVerificationStats()
    {
        $total = PesertaDidik::count();
        $verified = PesertaDidik::where('verification_status', 'verified')->count();
        $pending = PesertaDidik::where('verification_status', 'pending')->count();
        $rejected = PesertaDidik::where('verification_status', 'rejected')->count();

        return [
            'total' => $total,
            'verified' => $verified,
            'pending' => $pending,
            'rejected' => $rejected,
            'verifiedPercent' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
        ];
    }

    public function render()
    {
        $registrationStats = $this->getRegistrationStats();
        $dailyRegistrations = $this->getDailyRegistrations();
        $registrationByJalur = $this->getRegistrationByJalur();
        $topSchools = $this->getTopSchools();
        $statusBreakdown = $this->getStatusBreakdown();
        $verificationStats = $this->getVerificationStats();

        return view('livewire.dashboard.admin-dashboard', [
            // Legacy data
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'activeSessions' => LoginSession::count(),
            'usersByRole' => User::selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
            'roleSettings' => RoleSetting::all(),
            'recentLogins' => LoginSession::with('user')
                ->orderBy('last_activity', 'desc')
                ->limit(5)
                ->get(),

            // New analytics data
            'registrationStats' => $registrationStats,
            'dailyRegistrations' => $dailyRegistrations,
            'registrationByJalur' => $registrationByJalur,
            'topSchools' => $topSchools,
            'statusBreakdown' => $statusBreakdown,
            'verificationStats' => $verificationStats,
            'totalSekolahSMP' => SekolahMenengahPertama::count(),
            'totalJalur' => JalurPendaftaran::count(),
            'totalPesertaDidik' => PesertaDidik::count(),
        ]);
    }
}
