<?php

namespace App\Livewire\Opsmp;

use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\SekolahMenengahPertama;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Daya Tampung')]
class DayaTampung extends Component
{
    public $sekolah;

    public $form = [
        'jumlah_rombel' => 0,
        'daya_tampung' => 0,
    ];

    public function mount()
    {
        $this->sekolah = SekolahMenengahPertama::find(auth()->user()->sekolah_id);

        if (!$this->sekolah) {
            abort(403, 'Anda tidak terhubung dengan data sekolah.');
        }

        $this->form = [
            'jumlah_rombel' => $this->sekolah->jumlah_rombel,
            'daya_tampung' => $this->sekolah->daya_tampung,
        ];
    }

    public function update()
    {
        $this->validate([
            'form.jumlah_rombel' => 'required|integer|min:0',
            'form.daya_tampung' => 'required|integer|min:0',
        ]);

        if ($this->sekolah->is_locked_daya_tampung) {
            $this->addError('locked', 'Data tampung terkunci. Hubungi operator dinas untuk mengubah.');
            return;
        }

        $this->sekolah->update([
            'jumlah_rombel' => $this->form['jumlah_rombel'],
            'daya_tampung' => $this->form['daya_tampung'],
        ]);

        // Refresh sekolah data
        $this->sekolah->refresh();

        session()->flash('message', 'Data daya tampung berhasil diperbarui.');
    }

    public function getStatisticsProperty()
    {
        $totalDayaTampung = $this->sekolah->daya_tampung;
        $jalurs = JalurPendaftaran::where('aktif', true)->get();

        $stats = [];
        $totalTerdaftar = 0;

        foreach ($jalurs as $jalur) {
            $kuotaPercentage = $jalur->kuota ?? 0;
            $kuotaSlot = (int) floor(($kuotaPercentage / 100) * $totalDayaTampung);

            $terdaftar = Pendaftaran::where('sekolah_menengah_pertama_id', $this->sekolah->sekolah_id)
                ->where('jalur_pendaftaran_id', $jalur->id)
                ->count();

            $sisa = max(0, $kuotaSlot - $terdaftar);
            $percentage = $kuotaSlot > 0 ? min(100, round(($terdaftar / $kuotaSlot) * 100)) : 0;

            $stats[] = [
                'id' => $jalur->id,
                'nama' => $jalur->nama,
                'kuota_persen' => $kuotaPercentage,
                'kuota_slot' => $kuotaSlot,
                'terdaftar' => $terdaftar,
                'sisa' => $sisa,
                'percentage' => $percentage,
            ];

            $totalTerdaftar += $terdaftar;
        }

        return [
            'jalurs' => $stats,
            'total_daya_tampung' => $totalDayaTampung,
            'total_terdaftar' => $totalTerdaftar,
            'total_sisa' => max(0, $totalDayaTampung - $totalTerdaftar),
        ];
    }

    public function render()
    {
        return view('livewire.opsmp.daya-tampung', [
            'statistics' => $this->statistics,
        ]);
    }
}
