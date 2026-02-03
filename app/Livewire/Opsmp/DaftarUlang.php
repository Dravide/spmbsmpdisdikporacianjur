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
        ['start' => '10:00', 'end' => '12:00']
    ];

    public function mount()
    {
        $this->generateDateStart = date('Y-m-d', strtotime('+1 day'));
        // Initialize Jalur Settings
        $jalurs = \App\Models\JalurPendaftaran::all();
        foreach ($jalurs as $jalur) {
            $this->generateJalurSettings[$jalur->id] = $this->generateDateStart;
        }
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

    public function generateSchedule()
    {
        $user = Auth::user();
        if (!$user)
            return;

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

        // Get students who passed
        $lulusStudents = \App\Models\Pengumuman::where('sekolah_menengah_pertama_id', $user->sekolah_id)
            ->where('status', 'lulus')
            ->get();

        if ($lulusStudents->isEmpty()) {
            $this->dispatch('swal:modal', [
                'type' => 'warning',
                'title' => 'Tidak Ada Data',
                'text' => 'Belum ada siswa yang dinyatakan lulus.',
            ]);
            return;
        }

        // Delete existing schedule
        DaftarUlangModel::where('sekolah_menengah_pertama_id', $user->sekolah_id)->delete();

        if ($this->generateMode == 'auto') {
            $totalStudents = $lulusStudents->count();
            $studentsPerDay = ceil($totalStudents / $this->generateDays);

            $currentDate = \Carbon\Carbon::parse($this->generateDateStart);
            $studentCount = 0;
            $nomorUrut = 1;

            foreach ($lulusStudents as $student) {
                if ($studentCount >= $studentsPerDay) {
                    $currentDate->addDay();
                    $studentCount = 0;
                }

                $this->createSchedule($user->sekolah_id, $student, $currentDate->format('Y-m-d'), null, null, $nomorUrut);
                $studentCount++;
                $nomorUrut++;
            }
        } elseif ($this->generateMode == 'jalur') {
            $nomorUrut = 1;
            foreach ($lulusStudents as $student) {
                $jalurId = $student->jalur_pendaftaran_id;
                $date = $this->generateJalurSettings[$jalurId] ?? $this->generateDateStart;

                $this->createSchedule($user->sekolah_id, $student, $date, null, null, $nomorUrut);
                $nomorUrut++;
            }
        } elseif ($this->generateMode == 'sesi') {
            $totalStudents = $lulusStudents->count();
            $slotsPerDay = count($this->generateSessions);
            $totalSlots = $this->generateDays * $slotsPerDay;
            $studentsPerSlot = ceil($totalStudents / $totalSlots);

            $currentDate = \Carbon\Carbon::parse($this->generateDateStart);
            $studentCount = 0;
            $currentSessionIndex = 0;
            $nomorUrut = 1;

            foreach ($lulusStudents as $student) {
                if ($studentCount >= $studentsPerSlot) {
                    // Move to next slot
                    $studentCount = 0;
                    $currentSessionIndex++;

                    // If sessions for the day are exhausted, move to next day
                    if ($currentSessionIndex >= $slotsPerDay) {
                        $currentDate->addDay();
                        $currentSessionIndex = 0;
                    }
                }

                $session = $this->generateSessions[$currentSessionIndex];

                $this->createSchedule(
                    $user->sekolah_id,
                    $student,
                    $currentDate->format('Y-m-d'),
                    $session['start'],
                    $session['end'],
                    $nomorUrut
                );

                $studentCount++;
                $nomorUrut++;
            }
        }

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Jadwal daftar ulang berhasil digenerate.',
        ]);

        $this->dispatch('close-modal', ['id' => 'generateModal']);
        $this->dispatch('refresh');
    }

    public function resetData()
    {
        $user = Auth::user();
        if (!$user)
            return;

        DaftarUlangModel::where('sekolah_menengah_pertama_id', $user->sekolah_id)->delete();

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Semua jadwal daftar ulang berhasil dihapus.',
        ]);

        $this->dispatch('refresh');
    }

    private function createSchedule($sekolahId, $student, $date, $startTime = null, $endTime = null, $nomorUrut = null)
    {
        DaftarUlangModel::create([
            'sekolah_menengah_pertama_id' => $sekolahId,
            'pengumuman_id' => $student->id,
            'peserta_didik_id' => $student->peserta_didik_id,
            'tanggal' => $date,
            'waktu_mulai' => $startTime ?? $this->generateTimeStart,
            'waktu_selesai' => $endTime ?? $this->generateTimeEnd,
            'lokasi' => $this->generateLocation,
            'status' => 'belum',
            'nomor_urut' => $nomorUrut,
        ]);
    }

    public function markAsSudah($id)
    {
        $data = DaftarUlangModel::find($id);
        if ($data) {
            $data->update(['status' => 'sudah']);
            $this->dispatch('swal:toast', [
                'type' => 'success',
                'title' => 'Status diperbarui',
                'text' => 'Siswa ditandai sudah daftar ulang.'
            ]);
        }
    }

    public function markAsBelum($id)
    {
        $data = DaftarUlangModel::find($id);
        if ($data) {
            $data->update(['status' => 'belum']);
            $this->dispatch('swal:toast', [
                'type' => 'info',
                'title' => 'Status diperbarui',
                'text' => 'Status daftar ulang dikembalikan.'
            ]);
        }
    }

    public function downloadDaftarHadir()
    {
        $user = Auth::user();
        if (!$user)
            return;

        $query = DaftarUlangModel::with(['pesertaDidik.sekolah', 'pengumuman.jalur', 'pengumuman.pendaftaran'])
            ->where('sekolah_menengah_pertama_id', $user->sekolah_id);

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

        $items = $query->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->orderBy('nomor_urut', 'asc')
            ->get();

        if ($items->isEmpty()) {
            $this->dispatch('swal:modal', [
                'type' => 'warning',
                'title' => 'Data Kosong',
                'text' => 'Tidak ada data untuk dicetak sesuai filter yang dipilih.'
            ]);
            return;
        }

        // Setup QR Code Renderer
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(120),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);

        $groupedData = $items->groupBy(function ($item) {
            return $item->tanggal;
        })->map(function ($dateGroup) {
            return $dateGroup->groupBy(function ($item) {
                return $item->waktu_mulai . ' - ' . $item->waktu_selesai;
            });
        });

        $finalData = [];
        foreach ($groupedData as $date => $timeGroups) {
            foreach ($timeGroups as $timeRange => $timeItems) {
                $sessionData = [];
                foreach ($timeItems as $item) {
                    $nomorPeserta = $item->pengumuman->pendaftaran->nomor_pendaftaran ?? $item->pesertaDidik->nisn;

                    // Generate QR Code
                    $qrContent = $nomorPeserta;
                    $qrCode = $writer->writeString($qrContent);
                    $qrCodeBase64 = base64_encode($qrCode);

                    $sessionData[] = [
                        'nomor_peserta' => $nomorPeserta,
                        'nama' => $item->pesertaDidik->nama,
                        'asal_sekolah' => $item->pesertaDidik->sekolah->nama ?? 'Umum',
                        'jalur' => $item->pengumuman->jalur->nama ?? '-',
                        'qr_code' => $qrCodeBase64
                    ];
                }
                $finalData[$date][$timeRange] = $sessionData;
            }
        }

        $sekolah = $user->sekolah;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.daftar-hadir-daftar-ulang', [
            'groupedData' => $finalData,
            'sekolah' => $sekolah,
            'filterDateStart' => $this->dateStart,
            'filterDateEnd' => $this->dateEnd,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'daftar-hadir-' . date('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $user = Auth::user();
        if (!$user)
            return view('livewire.opsmp.daftar-ulang', ['daftarUlangs' => []]);

        $query = DaftarUlangModel::with(['pesertaDidik', 'pengumuman.jalur'])
            ->where('sekolah_menengah_pertama_id', $user->sekolah_id);

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

        $daftarUlangs = $query->orderBy('tanggal', 'asc')->orderBy('waktu_mulai', 'asc')->paginate(10);

        return view('livewire.opsmp.daftar-ulang', [
            'daftarUlangs' => $daftarUlangs
        ]);
    }
}
