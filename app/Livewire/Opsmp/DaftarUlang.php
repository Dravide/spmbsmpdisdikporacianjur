<?php

namespace App\Livewire\Opsmp;

use App\Models\DaftarUlang as DaftarUlangModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Jadwal Daftar Ulang')]
class DaftarUlang extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = '';

    public $dateStart = '';

    public $dateEnd = '';

    // Generation Properties
    public $generateMode = 'auto'; // auto, jalur, sesi

    public $generateDateStart;

    public $generateDays = 3;

    public $generateTimeStart = '08:00';

    public $generateTimeEnd = '14:00';

    public $generateLocation = 'Kampus SMP';

    public $generateJalurSettings = []; // [jalur_id => date]

    public $generateSessions = [
        ['start' => '08:00', 'end' => '10:00'],
        ['start' => '08:00', 'end' => '10:00'],
        ['start' => '10:00', 'end' => '12:00'],
    ];

    // Verification Modal
    public $showVerificationModal = false;
    public $selectedDaftarUlangId;
    public $verificationChecklist = []; // [ 'Label' => true/false ]
    public $studentName;
    public $verificationNotes;

    // Settings Modal
    public $showSettingsModal = false;
    public $syaratDaftarUlang = '';

    public function mount()
    {
        $this->generateDateStart = date('Y-m-d', strtotime('+1 day'));
        // Initialize Jalur Settings
        $jalurs = \App\Models\JalurPendaftaran::all();
        foreach ($jalurs as $jalur) {
            $this->generateJalurSettings[$jalur->id] = $this->generateDateStart;
        }

        // Load school specific requirements
        if (Auth::check() && Auth::user()->sekolah) {
            $this->syaratDaftarUlang = Auth::user()->sekolah->syarat_daftar_ulang;
        }
    }

    public function openSettingsModal()
    {
        if (Auth::check() && Auth::user()->sekolah) {
            $this->syaratDaftarUlang = Auth::user()->sekolah->syarat_daftar_ulang;
        }
        $this->showSettingsModal = true;
        $this->dispatch('open-modal', ['id' => 'settingsModal']);
    }

    public function closeSettingsModal()
    {
        $this->showSettingsModal = false;
        $this->dispatch('close-modal', ['id' => 'settingsModal']);
    }

    public function saveSettings()
    {
        $user = Auth::user();
        if (!$user || !$user->sekolah)
            return;

        $user->sekolah->update([
            'syarat_daftar_ulang' => $this->syaratDaftarUlang
        ]);

        $this->dispatch('swal:toast', [
            'type' => 'success',
            'title' => 'Tersimpan',
            'text' => 'Persyaratan daftar ulang berhasil disimpan.',
        ]);

        $this->closeSettingsModal();
    }

    public function openVerificationModal($id)
    {
        $this->selectedDaftarUlangId = $id;
        $daftarUlang = DaftarUlangModel::with('pesertaDidik')->find($id);

        if (!$daftarUlang)
            return;

        $this->studentName = $daftarUlang->pesertaDidik->nama;

        // Load School Requirements (ignore global)
        $user = Auth::user();
        $schoolReqs = ($user && $user->sekolah) ? $user->sekolah->syarat_daftar_ulang : '';

        $requirements = [];

        if ($schoolReqs) {
            $lines = explode("\n", $schoolReqs);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    // Remove leading dash/bullet if present
                    $cleanLine = ltrim($line, "-• ");
                    $requirements[$cleanLine] = false;
                }
            }
        }

        // Merge with existing saved state if any
        if (!empty($daftarUlang->checklist_dokumen)) {
            foreach ($daftarUlang->checklist_dokumen as $key => $val) {
                if (isset($requirements[$key])) {
                    $requirements[$key] = $val;
                } else {
                    // Keep old keys just in case requirements changed
                }
            }
        }

        // If no requirements configured, maybe add default?
        if (empty($requirements)) {
            $requirements['Dokumen Lengkap'] = false;
        }

        $this->verificationChecklist = $requirements;
        $this->showVerificationModal = true;

        $this->dispatch('open-modal', ['id' => 'verificationModal']);
    }

    public function closeVerificationModal()
    {
        $this->showVerificationModal = false;
        $this->reset(['selectedDaftarUlangId', 'verificationChecklist', 'studentName']);
        $this->dispatch('close-modal', ['id' => 'verificationModal']);
    }

    public function saveVerification()
    {
        $daftarUlang = DaftarUlangModel::find($this->selectedDaftarUlangId);
        if (!$daftarUlang)
            return;

        // Check if all checked? Or just save state?
        // User: "Jika semua (atau yang wajib) terpenuhi, status berubah jadi 'Sudah'"
        // Let's assume all listed are mandatory for now.

        $allChecked = !in_array(false, $this->verificationChecklist);

        $daftarUlang->update([
            'checklist_dokumen' => $this->verificationChecklist,
            'status' => $allChecked ? 'sudah' : 'belum',
        ]);

        if ($allChecked) {
            $this->dispatch('swal:toast', [
                'type' => 'success',
                'title' => 'Verifikasi Berhasil',
                'text' => 'Status siswa diperbarui menjadi SUDAH daftar ulang.',
            ]);
        } else {
            $this->dispatch('swal:toast', [
                'type' => 'info',
                'title' => 'Disimpan',
                'text' => 'Checklist disimpan. Status masih BELUM lengkap.',
            ]);
        }

        $this->closeVerificationModal();
    }

    public function markAsBelum($id)
    {
        $daftarUlang = DaftarUlangModel::find($id);
        if ($daftarUlang) {
            $daftarUlang->update(['status' => 'belum', 'checklist_dokumen' => null]);
            $this->dispatch('swal:toast', [
                'type' => 'info',
                'title' => 'Status Diubah',
                'text' => 'Status siswa diubah menjadi BELUM daftar ulang.',
            ]);
        }
    }

    public function generateSchedule()
    {
        // Validation
        if ($this->generateMode == 'auto') {
            $this->validate([
                'generateDateStart' => 'required|date',
                'generateDays' => 'required|integer|min:1',
            ]);
        } elseif ($this->generateMode == 'jalur') {
            $this->validate([
                'generateJalurSettings.*' => 'required|date',
            ]);
        } elseif ($this->generateMode == 'sesi') {
            $this->validate([
                'generateDateStart' => 'required|date',
                'generateDays' => 'required|integer|min:1',
                'generateSessions' => 'required|array|min:1',
                'generateSessions.*.start' => 'required',
                'generateSessions.*.end' => 'required',
            ]);
        }

        $user = Auth::user();
        if (!$user || !$user->sekolah) {
            $this->dispatch('swal:toast', ['type' => 'error', 'title' => 'Error', 'text' => 'Sekolah tidak ditemukan.']);
            return;
        }

        $sekolahId = $user->sekolah->sekolah_id;

        // Delete existing schedules for this school
        DaftarUlangModel::where('sekolah_menengah_pertama_id', $sekolahId)->delete();

        // Get all LULUS students for this school
        $pengumumans = \App\Models\Pengumuman::where('sekolah_menengah_pertama_id', $sekolahId)
            ->where('status', 'Lulus')
            ->with('pesertaDidik')
            ->get();

        if ($pengumumans->isEmpty()) {
            $this->dispatch('swal:toast', ['type' => 'warning', 'title' => 'Tidak ada data', 'text' => 'Tidak ada siswa LULUS untuk di-generate.']);
            return;
        }

        $nomorUrut = 1;

        if ($this->generateMode == 'auto') {
            $studentsPerDay = ceil($pengumumans->count() / $this->generateDays);
            $currentDate = \Carbon\Carbon::parse($this->generateDateStart);
            $dayCounter = 0;

            foreach ($pengumumans as $index => $pengumuman) {
                if ($index > 0 && $index % $studentsPerDay == 0) {
                    $currentDate->addDay();
                    $dayCounter++;
                    if ($dayCounter >= $this->generateDays) {
                        $currentDate = \Carbon\Carbon::parse($this->generateDateStart)->addDays($this->generateDays - 1);
                    }
                }

                DaftarUlangModel::create([
                    'sekolah_menengah_pertama_id' => $sekolahId,
                    'pengumuman_id' => $pengumuman->id,
                    'peserta_didik_id' => $pengumuman->peserta_didik_id,
                    'tanggal' => $currentDate->format('Y-m-d'),
                    'waktu_mulai' => $this->generateTimeStart,
                    'waktu_selesai' => $this->generateTimeEnd,
                    'lokasi' => $this->generateLocation,
                    'status' => 'belum',
                    'nomor_urut' => $nomorUrut++,
                ]);
            }
        } elseif ($this->generateMode == 'jalur') {
            foreach ($pengumumans as $pengumuman) {
                $jalurId = $pengumuman->jalur_id;
                $tanggal = $this->generateJalurSettings[$jalurId] ?? $this->generateDateStart;

                DaftarUlangModel::create([
                    'sekolah_menengah_pertama_id' => $sekolahId,
                    'pengumuman_id' => $pengumuman->id,
                    'peserta_didik_id' => $pengumuman->peserta_didik_id,
                    'tanggal' => $tanggal,
                    'waktu_mulai' => $this->generateTimeStart,
                    'waktu_selesai' => $this->generateTimeEnd,
                    'lokasi' => $this->generateLocation,
                    'status' => 'belum',
                    'nomor_urut' => $nomorUrut++,
                ]);
            }
        } elseif ($this->generateMode == 'sesi') {
            $totalSlots = $this->generateDays * count($this->generateSessions);
            $studentsPerSlot = ceil($pengumumans->count() / $totalSlots);

            $studentIndex = 0;
            for ($day = 0; $day < $this->generateDays; $day++) {
                $currentDate = \Carbon\Carbon::parse($this->generateDateStart)->addDays($day);
                foreach ($this->generateSessions as $session) {
                    for ($i = 0; $i < $studentsPerSlot && $studentIndex < $pengumumans->count(); $i++) {
                        $pengumuman = $pengumumans[$studentIndex];
                        DaftarUlangModel::create([
                            'sekolah_menengah_pertama_id' => $sekolahId,
                            'pengumuman_id' => $pengumuman->id,
                            'peserta_didik_id' => $pengumuman->peserta_didik_id,
                            'tanggal' => $currentDate->format('Y-m-d'),
                            'waktu_mulai' => $session['start'],
                            'waktu_selesai' => $session['end'],
                            'lokasi' => $this->generateLocation,
                            'status' => 'belum',
                            'nomor_urut' => $nomorUrut++,
                        ]);
                        $studentIndex++;
                    }
                }
            }
        }

        $this->dispatch('swal:toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jadwal daftar ulang berhasil di-generate untuk ' . $pengumumans->count() . ' siswa.',
        ]);

        $this->dispatch('close-modal', ['id' => 'generateModal']);
    }

    public function addSession()
    {
        $this->generateSessions[] = ['start' => '08:00', 'end' => '10:00'];
    }

    public function removeSession($index)
    {
        unset($this->generateSessions[$index]);
        $this->generateSessions = array_values($this->generateSessions);
    }

    public function resetData()
    {
        $user = Auth::user();
        if (!$user || !$user->sekolah)
            return;

        DaftarUlangModel::where('sekolah_menengah_pertama_id', $user->sekolah->sekolah_id)->delete();

        $this->dispatch('swal:toast', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Semua data jadwal daftar ulang telah dihapus.',
        ]);
    }

    public function render()
    {
        $user = Auth::user();
        $sekolahId = $user && $user->sekolah ? $user->sekolah->sekolah_id : null;

        $query = DaftarUlangModel::query()
            ->where('sekolah_menengah_pertama_id', $sekolahId)
            ->with(['pesertaDidik', 'pengumuman.jalur']);

        if ($this->search) {
            $query->whereHas('pesertaDidik', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->dateStart) {
            $query->whereDate('tanggal', '>=', $this->dateStart);
        }

        if ($this->dateEnd) {
            $query->whereDate('tanggal', '<=', $this->dateEnd);
        }

        $daftarUlangs = $query->orderBy('tanggal')->orderBy('nomor_urut')->paginate(15);

        return view('livewire.opsmp.daftar-ulang', [
            'daftarUlangs' => $daftarUlangs,
        ]);
    }
}
