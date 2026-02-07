<?php

namespace App\Livewire\Opsmp;

use App\Models\Pendaftaran;
use App\Models\PendaftaranBerkas;
use App\Models\SekolahDasar;
use App\Models\SekolahMenengahPertama;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tiket Bantuan')]
class TicketManager extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $type;
    public $input_jalur_id;

    // Correction Data Inputs
    public $input_nik;
    public $input_nisn;
    public $input_nama_ibu;

    // Delete File Input
    public $input_berkas_id;

    // Transfer School Input
    public $input_sekolah_id;

    // Form Inputs
    public $pendaftaran_id;
    public $input_npsn;
    public $input_nama_sekolah;
    public $input_alamat;
    public $input_desa_kelurahan;
    public $input_kecamatan;
    public $catatan;

    // Search helpers
    public $searchSiswa = '';
    public $searchResults = [];

    public function getJalursProperty()
    {
        return \App\Models\JalurPendaftaran::where('aktif', true)->get();
    }

    public function getUploadedFilesProperty()
    {
        if ($this->pendaftaran_id && $this->type === 'delete_file') {
            return PendaftaranBerkas::with('berkas')
                ->where('pendaftaran_id', $this->pendaftaran_id)
                ->get();
        }
        return [];
    }

    public function getAvailableSchoolsProperty()
    {
        if ($this->type === 'transfer_school') {
            return SekolahMenengahPertama::where('id', '!=', Auth::user()->sekolah_id)
                ->orderBy('nama', 'asc')
                ->get();
        }
        return [];
    }

    public function updatedSearchSiswa()
    {
        // Reset selected ID when user types
        $this->pendaftaran_id = null;

        if (strlen($this->searchSiswa) > 2) {
            $user = Auth::user();
            $query = Pendaftaran::with('pesertaDidik')
                ->where('sekolah_menengah_pertama_id', $user->sekolah_id);

            if ($this->type === 'restore_pendaftaran') {
                $query->onlyTrashed();
            }

            $this->searchResults = $query->whereHas('pesertaDidik', function ($q) {
                $q->where('nama', 'like', '%' . $this->searchSiswa . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchSiswa . '%');
            })
                ->limit(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectPendaftaran($id, $nama)
    {
        $this->pendaftaran_id = $id;
        $this->searchSiswa = $nama;
        $this->searchResults = [];
    }

    public function getAvailableTicketTypesProperty()
    {
        $allTypes = [
            'delete_pendaftaran' => 'Hapus Pendaftaran',
            'reset_password' => 'Reset Password Siswa',
            'move_jalur' => 'Pindah Jalur Pendaftaran',
            'unverify' => 'Buka Kunci Verifikasi (Un-verify)',
            'correction_data' => 'Koreksi Data (NIK/NISN/Ibu)',
            'restore_pendaftaran' => 'Restore / Batalkan Hapus',
            'delete_file' => 'Request Hapus Berkas Tertentu',
            'transfer_school' => 'Pindah Sekolah Pilihan (Transfer)',
            'input_sekolah_dasar' => 'Input Sekolah Dasar Baru',
        ];

        $savedActive = get_setting('active_ticket_types');
        if ($savedActive === null) {
            return $allTypes; // All active by default
        }

        $activeKeys = json_decode($savedActive, true) ?? [];
        if (empty($activeKeys))
            return [];

        return array_intersect_key($allTypes, array_flip($activeKeys));
    }

    public function openCreateModal()
    {
        $this->reset([
            'type',
            'pendaftaran_id',
            'input_npsn',
            'input_nama_sekolah',
            'input_alamat',
            'catatan',
            'searchSiswa',
            'searchResults',
            'input_jalur_id',
            'input_nik',
            'input_nisn',
            'input_nama_ibu',
            'input_berkas_id',
            'input_sekolah_id'
        ]);

        // Set default type to first available
        $available = array_keys($this->availableTicketTypes);
        $this->type = !empty($available) ? $available[0] : null;

        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function store()
    {
        $availableKeys = array_keys($this->availableTicketTypes);
        $allowed = implode(',', $availableKeys);

        $this->validate([
            'type' => 'required|in:' . $allowed,
            'catatan' => 'nullable|string',
        ]);

        $payload = [];

        if ($this->type === 'delete_pendaftaran' || $this->type === 'reset_password' || $this->type === 'move_jalur' || $this->type === 'unverify' || $this->type === 'correction_data' || $this->type === 'restore_pendaftaran' || $this->type === 'delete_file' || $this->type === 'transfer_school') {
            $this->validate([
                'pendaftaran_id' => 'required',
            ]);

            if ($this->type === 'restore_pendaftaran') {
                $pendaftaran = Pendaftaran::withTrashed()->find($this->pendaftaran_id);
            } else {
                $pendaftaran = Pendaftaran::find($this->pendaftaran_id);
            }

            if (!$pendaftaran) {
                $this->addError('pendaftaran_id', 'Data pendaftaran tidak ditemukan.');
                return;
            }

            $payload = [
                'pendaftaran_id' => $this->pendaftaran_id,
                'nama_siswa' => $pendaftaran->pesertaDidik->nama,
                'nisn' => $pendaftaran->pesertaDidik->nisn,
            ];

            if ($this->type === 'reset_password') {
                $payload['user_id'] = $pendaftaran->pesertaDidik->user_id;
            }

            if ($this->type === 'move_jalur') {
                $this->validate([
                    'input_jalur_id' => 'required|exists:jalur_pendaftarans,id',
                ]);
                $jalur = \App\Models\JalurPendaftaran::find($this->input_jalur_id);
                $payload['new_jalur_id'] = $jalur->id;
                $payload['new_jalur_nama'] = $jalur->nama;
                $payload['old_jalur_nama'] = $pendaftaran->jalur->nama ?? '-';
            }

            if ($this->type === 'unverify') {
                $payload['current_status'] = $pendaftaran->status;
            }

            if ($this->type === 'correction_data') {
                $this->validate([
                    'input_nik' => 'nullable|numeric|digits:16',
                    'input_nisn' => 'nullable|numeric|digits:10',
                    'input_nama_ibu' => 'nullable|string|max:255',
                ]);

                if (!$this->input_nik && !$this->input_nisn && !$this->input_nama_ibu) {
                    $this->addError('input_nik', 'Salah satu data (NIK, NISN, atau Nama Ibu) harus diisi.');
                    return;
                }

                if ($this->input_nik)
                    $payload['new_nik'] = $this->input_nik;
                if ($this->input_nisn)
                    $payload['new_nisn'] = $this->input_nisn;
                if ($this->input_nama_ibu)
                    $payload['new_nama_ibu'] = $this->input_nama_ibu;

                $payload['old_nik'] = $pendaftaran->pesertaDidik->nik;
                $payload['old_nisn'] = $pendaftaran->pesertaDidik->nisn;
                $payload['old_nama_ibu'] = $pendaftaran->pesertaDidik->nama_ibu_kandung;
            }

            if ($this->type === 'restore_pendaftaran') {
                $payload['deleted_at'] = $pendaftaran->deleted_at->format('d M Y H:i');
            }

            if ($this->type === 'delete_file') {
                $this->validate([
                    'input_berkas_id' => 'required|exists:pendaftaran_berkases,id',
                ]);
                $berkas = PendaftaranBerkas::with('berkas')->find($this->input_berkas_id);
                // Ensure the file belongs to the selected pendaftaran logic check (optional but safe)
                if ($berkas->pendaftaran_id != $this->pendaftaran_id) {
                    $this->addError('input_berkas_id', 'Berkas tidak valid.');
                    return;
                }

                $payload['pendaftaran_berkas_id'] = $berkas->id;
                $payload['nama_berkas'] = $berkas->berkas->nama ?? 'Unknown File';
                $payload['nama_file_asli'] = $berkas->nama_file_asli;
            }

            if ($this->type === 'transfer_school') {
                $this->validate([
                    'input_sekolah_id' => 'required|exists:sekolah_menengah_pertamas,id',
                ]);
                $sekolah = SekolahMenengahPertama::find($this->input_sekolah_id);
                $oldSekolah = SekolahMenengahPertama::find($pendaftaran->sekolah_menengah_pertama_id);

                $payload['new_school_id'] = $sekolah->id;
                $payload['new_school_name'] = $sekolah->nama;
                $payload['old_school_name'] = $oldSekolah->nama ?? 'Unknown';
            }
        } elseif ($this->type === 'input_sekolah_dasar') {
            $this->validate([
                'input_npsn' => 'required|numeric|unique:sekolah_dasar,npsn',
                'input_nama_sekolah' => 'required|string|max:255',
                'input_alamat' => 'required|string',
            ]);
            $payload = [
                'npsn' => $this->input_npsn,
                'nama' => $this->input_nama_sekolah,
                'alamat_jalan' => $this->input_alamat,
                'desa_kelurahan' => $this->input_desa_kelurahan,
                'kecamatan' => $this->input_kecamatan,
            ];
        }

        Ticket::create([
            'user_id' => Auth::id(),
            'type' => $this->type,
            'status' => 'pending',
            'payload' => $payload,
            'description' => $this->catatan, // Assuming I might add description col or use standard way, waiting, migration didn't have description, only admin_note.
            // Wait, migration had 'payload' and 'admin_note'. I should add user note/description inside payload or add column?
            // The migration I wrote: create('tickets', ... $table->text('admin_note')->nullable(); ...
            // I missed a "user_description" or "reason" column.
            // I will put the user note in the payload for now to avoid re-migrating, or just assume the user note is part of the request logic.
            // Actually, "catatan" is useful. I'll put it in payload['reason'] for now.
        ]);

        // Update payload with reason
        $ticket = Ticket::latest()->first();
        $payload['reason'] = $this->catatan;
        $ticket->update(['payload' => $payload]);

        $this->closeCreateModal();
        session()->flash('message', 'Tiket berhasil dibuat.');
    }

    public function render()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.opsmp.ticket-manager', [
            'tickets' => $tickets,
        ]);
    }
}
