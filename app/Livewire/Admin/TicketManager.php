<?php

namespace App\Livewire\Admin;

use App\Models\Pendaftaran;
use App\Models\SekolahDasar;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Kelola Tiket Bantuan')]
class TicketManager extends Component
{
    use WithPagination;

    public $filterStatus = 'pending';
    public $search = '';

    protected $queryString = ['filterStatus', 'search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public $showApproveModal = false;
    public $selectedTicketId = null;
    public $selectedTicket = null;

    public function openApproveModal($id)
    {
        $this->selectedTicketId = $id;
        $this->selectedTicket = Ticket::with(['user.sekolahDasar', 'user.sekolahMenengahPertama'])->find($id);
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->selectedTicketId = null;
        $this->selectedTicket = null;
    }

    public function approve()
    {
        $ticket = Ticket::find($this->selectedTicketId);

        if (!$ticket || $ticket->status !== 'pending') {
            return;
        }

        // MAGIC EXECUTION
        try {
            if ($ticket->type === 'delete_pendaftaran') {
                $pendaftaran = Pendaftaran::find($ticket->payload['pendaftaran_id']);
                if ($pendaftaran) {
                    // Delete files
                    foreach ($pendaftaran->berkas as $berkas) {
                        if ($berkas->file_path && \Storage::disk('public')->exists($berkas->file_path)) {
                            \Storage::disk('public')->delete($berkas->file_path);
                        }
                        $berkas->delete();
                    }
                    $pendaftaran->delete();
                    $ticket->admin_note = 'Executed: Pendaftaran deleted successfully.';
                } else {
                    $ticket->admin_note = 'Warning: Pendaftaran not found (maybe already deleted).';
                }
            } elseif ($ticket->type === 'reset_password') {
                $user = User::find($ticket->payload['user_id']);
                if ($user) {
                    $newPassword = 'password123'; // Default reset password
                    $user->password = Hash::make($newPassword);
                    $user->save();
                    $ticket->admin_note = "Executed: Password reset to '$newPassword'.";
                } else {
                    $ticket->admin_note = 'Error: User not found.';
                }
            } elseif ($ticket->type === 'input_sekolah_dasar') {
                // Check if NPSN exists
                if (SekolahDasar::where('npsn', $ticket->payload['npsn'])->exists()) {
                    $ticket->admin_note = 'Error: Sekolah with this NPSN already exists.';
                    $ticket->status = 'rejected';
                    $ticket->save();
                    $this->closeApproveModal();
                    session()->flash('error', 'NPSN sudah ada.');
                    return;
                }

                // Create Sekolah Dasar
                SekolahDasar::create([
                    'sekolah_id' => \Illuminate\Support\Str::uuid(),
                    'npsn' => $ticket->payload['npsn'],
                    'nama' => $ticket->payload['nama'],
                    'alamat_jalan' => $ticket->payload['alamat_jalan'],
                    'desa_kelurahan' => $ticket->payload['desa_kelurahan'] ?? null,
                    'kecamatan' => $ticket->payload['kecamatan'] ?? null,
                    // Add defaults for required fields if any
                    'bentuk_pendidikan_id' => 5, // SD
                    'status_sekolah' => 'NEGERI', // Default or need input? Assuming Negeri for now or generic.
                ]);
                $ticket->admin_note = 'Executed: Sekolah Dasar created successfully.';
            } elseif ($ticket->type === 'move_jalur') {
                $pendaftaran = Pendaftaran::find($ticket->payload['pendaftaran_id']);
                if ($pendaftaran) {
                    $oldJalur = $pendaftaran->jalur->nama ?? 'Unknown';
                    $newJalurId = $ticket->payload['new_jalur_id'];
                    $newJalurName = $ticket->payload['new_jalur_nama'];

                    // DELETE ALL FILES
                    foreach ($pendaftaran->berkas as $berkas) {
                        if ($berkas->file_path && \Storage::disk('public')->exists($berkas->file_path)) {
                            \Storage::disk('public')->delete($berkas->file_path);
                        }
                        $berkas->delete();
                    }

                    $pendaftaran->jalur_pendaftaran_id = $newJalurId;
                    $pendaftaran->status = 'process';

                    $pendaftaran->save();
                    $ticket->admin_note = "Executed: Moved from '$oldJalur' to '$newJalurName'. Files deleted. Status reset to process.";
                } else {
                    $ticket->admin_note = 'Warning: Pendaftaran not found.';
                }
            } elseif ($ticket->type === 'unverify') {
                $pendaftaran = Pendaftaran::find($ticket->payload['pendaftaran_id']);
                if ($pendaftaran) {
                    $pendaftaran->status = 'process';
                    $pendaftaran->save();
                    $ticket->admin_note = "Executed: Status reset to 'process' (Un-verified).";
                } else {
                    $ticket->admin_note = 'Warning: Pendaftaran not found.';
                }
            } elseif ($ticket->type === 'correction_data') {
                $pendaftaran = Pendaftaran::find($ticket->payload['pendaftaran_id']);
                if ($pendaftaran && $pendaftaran->pesertaDidik) {
                    $peserta = $pendaftaran->pesertaDidik;
                    $changes = [];

                    if (isset($ticket->payload['new_nik'])) {
                        $peserta->nik = $ticket->payload['new_nik'];
                        $changes[] = 'NIK';
                    }
                    if (isset($ticket->payload['new_nisn'])) {
                        $peserta->nisn = $ticket->payload['new_nisn'];
                        $changes[] = 'NISN';
                    }
                    if (isset($ticket->payload['new_nama_ibu'])) {
                        $peserta->nama_ibu_kandung = $ticket->payload['new_nama_ibu'];
                        $changes[] = 'Nama Ibu';
                    }

                    $peserta->save();
                    $ticket->admin_note = "Executed: Updated " . implode(', ', $changes) . ".";
                } else {
                    $ticket->admin_note = 'Warning: Peserta Didik not found.';
                }
            } elseif ($ticket->type === 'restore_pendaftaran') {
                $pendaftaran = Pendaftaran::withTrashed()->find($ticket->payload['pendaftaran_id']);

                if ($pendaftaran) {
                    // Check if student has another active registration
                    $activeExists = Pendaftaran::where('peserta_didik_id', $pendaftaran->peserta_didik_id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($activeExists) {
                        $ticket->admin_note = "Failed: Student already has an active registration. Cannot restore.";
                        $ticket->status = 'rejected';
                        $ticket->save();
                        $this->closeApproveModal();
                        session()->flash('error', 'Gagal: Siswa sudah memiliki pendaftaran aktif. Tidak bisa restore.');
                        return;
                    }

                    $pendaftaran->restore();
                    $ticket->admin_note = "Executed: Pendaftaran restored successfully.";
                } else {
                    $ticket->admin_note = 'Warning: Pendaftaran not found.';
                }
            } elseif ($ticket->type === 'delete_file') {
                $berkasId = $ticket->payload['pendaftaran_berkas_id'];
                $pendaftaranBerkas = \App\Models\PendaftaranBerkas::find($berkasId);

                if ($pendaftaranBerkas) {
                    // Delete physical file
                    if ($pendaftaranBerkas->file_path && \Storage::disk('public')->exists($pendaftaranBerkas->file_path)) {
                        \Storage::disk('public')->delete($pendaftaranBerkas->file_path);
                    }

                    $fileName = $pendaftaranBerkas->berkas->nama ?? 'Unknown';
                    $pendaftaranBerkas->delete();

                    $ticket->admin_note = "Executed: File '$fileName' deleted successfully.";
                } else {
                    $ticket->admin_note = 'Warning: File/Record not found (maybe already deleted).';
                }
            } elseif ($ticket->type === 'transfer_school') {
                $pendaftaran = Pendaftaran::find($ticket->payload['pendaftaran_id']);
                if ($pendaftaran) {
                    $oldSchool = $ticket->payload['old_school_name'] ?? 'Unknown';
                    $newSchoolName = $ticket->payload['new_school_name'];

                    $pendaftaran->sekolah_menengah_pertama_id = $ticket->payload['new_school_id'];
                    $pendaftaran->status = 'process'; // Reset status for new school to verify
                    $pendaftaran->save();

                    $ticket->admin_note = "Executed: Transferred from '$oldSchool' to '$newSchoolName'. Status reset.";
                } else {
                    $ticket->admin_note = 'Warning: Pendaftaran not found.';
                }
            }

            $ticket->status = 'approved';
            $ticket->save();

            $this->closeApproveModal();
            session()->flash('message', 'Tiket disetujui dan aksi otomatis dieksekusi.');

        } catch (\Exception $e) {
            $ticket->admin_note = 'Error executing: ' . $e->getMessage();
            $ticket->status = 'rejected'; // Or keep pending? Let's reject if error.
            $ticket->save();
            $this->closeApproveModal();
            session()->flash('error', 'Gagal mengeksekusi tiket: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $ticket = Ticket::find($id);
        if ($ticket && $ticket->status === 'pending') {
            $ticket->status = 'rejected';
            $ticket->admin_note = 'Ditolak oleh Admin.';
            $ticket->save();
            session()->flash('message', 'Tiket ditolak.');
        }
    }

    public function render()
    {
        $tickets = Ticket::with(['user.sekolahDasar', 'user.sekolahMenengahPertama'])
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('sekolahDasar', function ($s) {
                            $s->where('nama', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('sekolahMenengahPertama', function ($s) {
                            $s->where('nama', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.ticket-manager', [
            'tickets' => $tickets
        ]);
    }
}
