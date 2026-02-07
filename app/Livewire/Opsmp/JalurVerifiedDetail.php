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

    // For akademik/non-akademik pathways input
    public $nilaiInputs = [];

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

        if (str_contains($jalurName, 'zonasi') || str_contains($jalurName, 'domisili') || $this->jalurId == 6 || $this->jalurId == 7 || $this->jalurId == 8) {
            // Zoning/Domicile: Prioritize closest distance
            $this->verifiedStudents = $pendaftarans->sortBy('jarak_meter')->values();
        } elseif (str_contains($jalurName, 'prestasi akademik & non-akademik')) {
            // Prestasi Akademik & Non-Akademik: Sort by nilai_test (Higher is better)
            // Tiebreaker: Total nilai raport (Higher is better) -> verification time (earlier is better)
            $this->verifiedStudents = $pendaftarans->map(function ($pendaftaran) {
                // Calculate total nilai raport from all berkas with number fields
                $raportTotal = 0;
                $raportCount = 0;
                foreach ($pendaftaran->berkas as $pb) {
                    if ($pb->form_data && $pb->berkas && $pb->berkas->form_fields) {
                        $formData = is_string($pb->form_data) ? json_decode($pb->form_data, true) : $pb->form_data;
                        $formFields = is_string($pb->berkas->form_fields) ? json_decode($pb->berkas->form_fields, true) : $pb->berkas->form_fields;

                        if (is_array($formData) && is_array($formFields)) {
                            foreach ($formFields as $field) {
                                if (isset($field['type']) && $field['type'] === 'number' && isset($field['name'])) {
                                    $value = floatval($formData[$field['name']] ?? 0);
                                    $raportTotal += $value;
                                    if ($value > 0) {
                                        $raportCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $pendaftaran->raport_total = $raportTotal;
                $pendaftaran->raport_count = $raportCount;

                return $pendaftaran;
            })->sortBy([
                ['nilai_test', 'desc'],        // Primary: nilai_test (higher is better)
                ['raport_total', 'desc'],      // Tiebreaker 1: raport total (higher is better)
                ['verified_at', 'asc'],       // Tiebreaker 2: verification time (earlier is better)
            ])->values();

            // Initialize nilaiInputs with existing values
            foreach ($this->verifiedStudents as $student) {
                if ($student->nilai_test !== null) {
                    $this->nilaiInputs[$student->id] = $student->nilai_test;
                }
            }
        } elseif (str_contains($jalurName, 'prestasi tahfidz quran')) {
            // Prestasi Tahfidz Quran: Sort by nilai_test (Higher is better)
            // Tiebreaker: Total nilai raport (Higher is better) -> verification time (earlier is better)
            $this->verifiedStudents = $pendaftarans->map(function ($pendaftaran) {
                // Calculate total nilai raport from all berkas with number fields
                $raportTotal = 0;
                $raportCount = 0;
                foreach ($pendaftaran->berkas as $pb) {
                    if ($pb->form_data && $pb->berkas && $pb->berkas->form_fields) {
                        $formData = is_string($pb->form_data) ? json_decode($pb->form_data, true) : $pb->form_data;
                        $formFields = is_string($pb->berkas->form_fields) ? json_decode($pb->berkas->form_fields, true) : $pb->berkas->form_fields;

                        if (is_array($formData) && is_array($formFields)) {
                            foreach ($formFields as $field) {
                                if (isset($field['type']) && $field['type'] === 'number' && isset($field['name'])) {
                                    $value = floatval($formData[$field['name']] ?? 0);
                                    $raportTotal += $value;
                                    if ($value > 0) {
                                        $raportCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $pendaftaran->raport_total = $raportTotal;
                $pendaftaran->raport_count = $raportCount;

                return $pendaftaran;
            })->sortBy([
                ['nilai_test', 'desc'],        // Primary: nilai_test (higher is better)
                ['raport_total', 'desc'],      // Tiebreaker 1: raport total (higher is better)
                ['verified_at', 'asc'],       // Tiebreaker 2: verification time (earlier is better)
            ])->values();

            // Initialize nilaiInputs with existing values
            foreach ($this->verifiedStudents as $student) {
                if ($student->nilai_test !== null) {
                    $this->nilaiInputs[$student->id] = $student->nilai_test;
                }
            }
        } elseif ((str_contains($jalurName, 'ranking') || str_contains($jalurName, 'prestasi')) && ! str_contains($jalurName, 'prestasi akademik & non-akademik') && ! str_contains($jalurName, 'prestasi tahfidz quran')) {
            // Ranking/Prestasi: Sort by Rank Score (Higher is better)
            // Tiebreaker: Total nilai raport (Higher is better)
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

                // Calculate total nilai raport from all berkas with number fields
                $raportTotal = 0;
                $raportCount = 0;
                foreach ($pendaftaran->berkas as $pb) {
                    if ($pb->form_data && $pb->berkas && $pb->berkas->form_fields) {
                        $formData = is_string($pb->form_data) ? json_decode($pb->form_data, true) : $pb->form_data;
                        $formFields = is_string($pb->berkas->form_fields) ? json_decode($pb->berkas->form_fields, true) : $pb->berkas->form_fields;

                        if (is_array($formData) && is_array($formFields)) {
                            foreach ($formFields as $field) {
                                // Sum all number fields (nilai fields)
                                if (isset($field['type']) && $field['type'] === 'number' && isset($field['name'])) {
                                    $value = floatval($formData[$field['name']] ?? 0);
                                    $raportTotal += $value;
                                    if ($value > 0) {
                                        $raportCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $pendaftaran->ranking_score = $score;
                $pendaftaran->ranking_details = $ranks;
                $pendaftaran->raport_total = $raportTotal;
                $pendaftaran->raport_count = $raportCount;

                return $pendaftaran;
            })->sortBy([
                ['ranking_score', 'desc'],     // Primary: ranking score (higher is better)
                ['raport_total', 'desc'],      // Tiebreaker: raport total (higher is better)
            ])->values();

        } elseif (str_contains($jalurName, 'akademik') || str_contains($jalurName, 'test') || str_contains($jalurName, 'ujian')) {
            // Akademik/Non-Akademik: Sort by nilai_test (Higher is better)
            // Tiebreaker: Total nilai raport (Higher is better) -> verification time (earlier is better)
            $this->verifiedStudents = $pendaftarans->map(function ($pendaftaran) {
                // Calculate total nilai raport from all berkas with number fields
                $raportTotal = 0;
                $raportCount = 0;
                foreach ($pendaftaran->berkas as $pb) {
                    if ($pb->form_data && $pb->berkas && $pb->berkas->form_fields) {
                        $formData = is_string($pb->form_data) ? json_decode($pb->form_data, true) : $pb->form_data;
                        $formFields = is_string($pb->berkas->form_fields) ? json_decode($pb->berkas->form_fields, true) : $pb->berkas->form_fields;

                        if (is_array($formData) && is_array($formFields)) {
                            foreach ($formFields as $field) {
                                if (isset($field['type']) && $field['type'] === 'number' && isset($field['name'])) {
                                    $value = floatval($formData[$field['name']] ?? 0);
                                    $raportTotal += $value;
                                    if ($value > 0) {
                                        $raportCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $pendaftaran->raport_total = $raportTotal;
                $pendaftaran->raport_count = $raportCount;

                return $pendaftaran;
            })->sortBy([
                ['nilai_test', 'desc'],        // Primary: nilai_test (higher is better)
                ['raport_total', 'desc'],      // Tiebreaker 1: raport total (higher is better)
                ['verified_at', 'asc'],       // Tiebreaker 2: verification time (earlier is better)
            ])->values();

            // Initialize nilaiInputs with existing values
            foreach ($this->verifiedStudents as $student) {
                if ($student->nilai_test !== null) {
                    $this->nilaiInputs[$student->id] = $student->nilai_test;
                }
            }

        } else {
            // Other paths: Prioritize verification time (First Come First Serve)
            // Tiebreaker: Jarak (closer is better) -> Total nilai raport (higher is better)
            $this->verifiedStudents = $pendaftarans->map(function ($pendaftaran) {
                // Calculate total nilai raport from all berkas with number fields
                $raportTotal = 0;
                $raportCount = 0;
                foreach ($pendaftaran->berkas as $pb) {
                    if ($pb->form_data && $pb->berkas && $pb->berkas->form_fields) {
                        $formData = is_string($pb->form_data) ? json_decode($pb->form_data, true) : $pb->form_data;
                        $formFields = is_string($pb->berkas->form_fields) ? json_decode($pb->berkas->form_fields, true) : $pb->berkas->form_fields;

                        if (is_array($formData) && is_array($formFields)) {
                            foreach ($formFields as $field) {
                                if (isset($field['type']) && $field['type'] === 'number' && isset($field['name'])) {
                                    $value = floatval($formData[$field['name']] ?? 0);
                                    $raportTotal += $value;
                                    if ($value > 0) {
                                        $raportCount++;
                                    }
                                }
                            }
                        }
                    }
                }

                $pendaftaran->raport_total = $raportTotal;
                $pendaftaran->raport_count = $raportCount;

                return $pendaftaran;
            })->sortBy([
                ['verified_at', 'asc'],       // Primary: verification time (earlier is better)
                ['jarak_meter', 'asc'],       // Tiebreaker 1: jarak (closer is better)
                ['raport_total', 'desc'],     // Tiebreaker 2: raport total (higher is better)
            ])->values();
        }
    }

    private function convertRankToPoints($rank)
    {
        if ($rank == 1) {
            return 3;
        }
        if ($rank == 2) {
            return 2;
        }
        if ($rank == 3) {
            return 1;
        }

        return 0; // Rank 4 and below get 0 points
    }

    public function saveNilaiTest($pendaftaranId)
    {
        $nilai = $this->nilaiInputs[$pendaftaranId] ?? null;

        if ($nilai !== null && $nilai !== '') {
            // Validate nilai_test
            if ($nilai < 0 || $nilai > 100) {
                $this->dispatch('swal:toast', [
                    'type' => 'error',
                    'title' => 'Nilai Tidak Valid',
                    'text' => 'Nilai test harus antara 0 dan 100.',
                ]);

                return;
            }

            $pendaftaran = Pendaftaran::find($pendaftaranId);
            if ($pendaftaran) {
                $pendaftaran->nilai_test = floatval($nilai);
                $pendaftaran->save();
            }
        }

        $this->loadVerifiedStudents();
        $this->dispatch('nilaiSaved');
    }

    // Method that receives nilai directly from JavaScript
    public function saveNilaiTestDirect($pendaftaranId, $nilai)
    {
        if ($nilai !== null && $nilai !== '') {
            // Validate nilai_test
            if ($nilai < 0 || $nilai > 100) {
                $this->dispatch('swal:toast', [
                    'type' => 'error',
                    'title' => 'Nilai Tidak Valid',
                    'text' => 'Nilai test harus antara 0 dan 100.',
                ]);

                return;
            }

            $pendaftaran = Pendaftaran::find($pendaftaranId);
            if ($pendaftaran) {
                $pendaftaran->nilai_test = floatval($nilai);
                $pendaftaran->save();
            }
        }

        $this->loadVerifiedStudents();
        $this->dispatch('nilaiSaved');
    }

    public function saveAllNilai()
    {
        $count = 0;
        $invalidCount = 0;
        foreach ($this->nilaiInputs as $pendaftaranId => $nilai) {
            if ($nilai !== null && $nilai !== '') {
                // Validate nilai_test
                if ($nilai < 0 || $nilai > 100) {
                    $invalidCount++;

                    continue;
                }

                $pendaftaran = Pendaftaran::find($pendaftaranId);
                if ($pendaftaran) {
                    $pendaftaran->nilai_test = floatval($nilai);
                    $pendaftaran->save();
                    $count++;
                }
            }
        }

        if ($invalidCount > 0) {
            $this->dispatch('swal:toast', [
                'type' => 'warning',
                'title' => 'Beberapa Nilai Tidak Valid',
                'text' => "Ada {$invalidCount} nilai yang tidak valid (harus antara 0 dan 100).",
            ]);
        }

        $this->loadVerifiedStudents();
        $this->dispatch('allNilaiSaved', ['count' => $count]);
    }

    // Method that receives nilai array directly from JavaScript
    public function saveAllNilaiDirect($nilaiData)
    {
        $count = 0;
        foreach ($nilaiData as $pendaftaranId => $nilai) {
            if ($nilai !== null && $nilai !== '') {
                $pendaftaran = Pendaftaran::find($pendaftaranId);
                if ($pendaftaran) {
                    $pendaftaran->nilai_test = floatval($nilai);
                    $pendaftaran->save();
                    $count++;
                }
            }
        }

        $this->loadVerifiedStudents();
        $this->dispatch('allNilaiSaved', ['count' => $count]);
    }

    public function processAnnouncement()
    {
        try {
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

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Process announcement failed: '.$e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::user()->id,
                'jalur_id' => $this->jalurId,
            ]);

            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat memproses pengumuman: '.$e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.opsmp.jalur-verified-detail');
    }
}
