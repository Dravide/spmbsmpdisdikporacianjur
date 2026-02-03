<?php

namespace App\Livewire\Opsmp;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Jalur Verified')]
class JalurVerifiedDetail extends Component
{
    public $jalurId;
    public $jalur;
    public $verifiedStudents = [];

    public $quotaSlot = 0;
    public $totalDayaTampung = 0;

    public function mount($id)
    {
        $this->jalurId = $id;
        $this->jalur = JalurPendaftaran::findOrFail($id);

        $this->calculateQuota();
        $this->loadVerifiedStudents();
    }

    public function calculateQuota()
    {
        $user = Auth::user();
        if ($user && $user->sekolah) {
            $this->totalDayaTampung = $user->sekolah->daya_tampung;
            $percentage = $this->jalur->kuota ?? 0;
            $this->quotaSlot = (int) floor(($percentage / 100) * $this->totalDayaTampung);
        }
    }

    public function loadVerifiedStudents()
    {
        $user = Auth::user();
        $sekolahId = $user->sekolah_id;

        $query = Pendaftaran::with(['pesertaDidik.sekolah', 'berkas.berkas'])
            ->where('sekolah_menengah_pertama_id', $sekolahId)
            ->where('jalur_pendaftaran_id', $this->jalurId)
            ->where('status', 'verified');

        $pendaftarans = $query->get();

        // Ranking Logic
        $jalurName = strtolower($this->jalur->nama);

        if (str_contains($jalurName, 'zonasi') || str_contains($jalurName, 'domisili')) {
            // Zoning/Domicile: Prioritize closest distance
            $this->verifiedStudents = $pendaftarans->sortBy('jarak_meter')->values();
        } elseif (str_contains($jalurName, 'ranking') || str_contains($jalurName, 'prestasi')) {
            // Ranking/Prestasi: Sort by Rank Score (Higher is better)
            $this->verifiedStudents = $pendaftarans->map(function ($pendaftaran) {
                // Find berkas 'Bukti Ranking'
                $rankingBerkas = $pendaftaran->berkas->first(function ($pb) {
                    return $pb->berkas && (
                        str_contains(strtolower($pb->berkas->nama), 'ranking') ||
                        str_contains(strtolower($pb->berkas->nama), 'prestasi')
                    );
                });

                $score = 0; // Default score
                $ranks = [];

                if ($rankingBerkas && $rankingBerkas->form_data) {
                    $formData = is_string($rankingBerkas->form_data) ? json_decode($rankingBerkas->form_data, true) : $rankingBerkas->form_data;

                    if (is_array($formData)) {
                        // Calculate score: Rank 1=3, Rank 2=2, Rank 3=1
                        $r1 = floatval($formData['rank_5_sm1'] ?? 0);
                        $r2 = floatval($formData['rank_5_sm2'] ?? 0);
                        $r3 = floatval($formData['rank_6_sm1'] ?? 0);

                        $p1 = $this->convertRankToPoints($r1);
                        $p2 = $this->convertRankToPoints($r2);
                        $p3 = $this->convertRankToPoints($r3);

                        // Only count if valid ranks provided
                        if ($r1 > 0 || $r2 > 0 || $r3 > 0) {
                            $score = $p1 + $p2 + $p3;
                            $ranks = [$r1, $r2, $r3];
                        }
                    }
                }

                $pendaftaran->ranking_score = $score;
                $pendaftaran->ranking_details = $ranks;
                return $pendaftaran;
            })->sortByDesc('ranking_score')->values();

        } else {
            // Other paths: Prioritize verification time (First Come First Serve)
            $this->verifiedStudents = $pendaftarans->sortBy('verified_at')->values();
        }
    }

    private function convertRankToPoints($rank)
    {
        if ($rank == 1)
            return 3;
        if ($rank == 2)
            return 2;
        if ($rank == 3)
            return 1;
        return 0; // Rank 4 and below get 0 points
    }

    public function processAnnouncement()
    {
        $this->calculateQuota();
        $this->loadVerifiedStudents();

        $quota = $this->quotaSlot;
        $count = 0;

        foreach ($this->verifiedStudents as $student) {
            $status = $count < $quota ? 'lulus' : 'tidak_lulus';

            \App\Models\Pengumuman::updateOrCreate(
                [
                    'pendaftaran_id' => $student->id,
                ],
                [
                    'sekolah_menengah_pertama_id' => $student->sekolah_menengah_pertama_id,
                    'jalur_pendaftaran_id' => $student->jalur_pendaftaran_id,
                    'peserta_didik_id' => $student->peserta_didik_id,
                    'status' => $status,
                    'keterangan' => $status === 'lulus' ? 'Lulus passing grade / kuota' : 'Tidak masuk kuota',
                ]
            );

            $count++;

            // Notification for Announcement
            if ($student->pesertaDidik) {
                $notifStatus = $status === 'lulus' ? 'diterima' : 'ditolak';
                $student->pesertaDidik->notify(
                    \App\Notifications\StatusChangedNotification::announcement($notifStatus)
                );
            }
        }

        session()->flash('message', 'Pengumuman berhasil diproses.');
        $this->dispatch('processed');
    }

    public function render()
    {
        return view('livewire.opsmp.jalur-verified-detail');
    }
}
