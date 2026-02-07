<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Kelola Tiket Bantuan</h4>
            <p class="text-muted mb-0">Setujui atau tolak permintaan dari Operator SMP.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-cross-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari Sekolah / User..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="pending">Menunggu Konfirmasi</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                        <option value="">Semua Status</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Pemohon</th>
                            <th>Tipe</th>
                            <th>Detail Permintaan</th>
                            <th>Status</th>
                            <th>Admin Note</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $ticket->user->name ?? 'Unknown User' }}</div>
                                    <small class="text-muted">{{ $ticket->user->sekolah->nama ?? 'No School' }}</small>
                                </td>
                                <td>
                                    @if($ticket->type == 'delete_pendaftaran')
                                        <span class="badge bg-danger">Hapus Pendaftaran</span>
                                    @elseif($ticket->type == 'reset_password')
                                        <span class="badge bg-warning text-dark">Reset Password</span>
                                    @elseif($ticket->type == 'input_sekolah_dasar')
                                        <span class="badge bg-info text-dark">Input Sekolah Dasar</span>
                                    @elseif($ticket->type == 'move_jalur')
                                        <span class="badge bg-primary">Pindah Jalur</span>
                                    @elseif($ticket->type == 'unverify')
                                        <span class="badge bg-danger">Buka Kunci Verifikasi</span>
                                    @elseif($ticket->type == 'correction_data')
                                        <span class="badge bg-warning text-dark">Koreksi Data</span>
                                    @elseif($ticket->type == 'restore_pendaftaran')
                                        <span class="badge bg-secondary">Restore Data</span>
                                    @elseif($ticket->type == 'delete_file')
                                        <span class="badge bg-danger">Hapus Berkas</span>
                                    @elseif($ticket->type == 'transfer_school')
                                        <span class="badge bg-info text-dark">Pindah Sekolah</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $ticket->type }}</span>
                                    @endif
                                </td>
                                <td style="max-width: 300px;">
                                    <small class="d-block text-muted">
                                        @foreach($ticket->payload as $key => $val)
                                            @if($key !== 'reason' && $key !== 'user_id')
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}<br>
                                            @endif
                                        @endforeach
                                        @if(isset($ticket->payload['reason']))
                                            <div class="mt-1 text-dark fst-italic bg-light p-1 rounded">
                                                "{{ $ticket->payload['reason'] }}"</div>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if($ticket->status == 'pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($ticket->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($ticket->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->admin_note ?? '-' }}</td>
                                <td>
                                    @if($ticket->status == 'pending')
                                        <button class="btn btn-sm btn-success" wire:click="openApproveModal({{ $ticket->id }})">
                                            <i class="fi fi-rr-check"></i> Setujui
                                        </button>
                                        <button class="btn btn-sm btn-danger" wire:click="reject({{ $ticket->id }})"
                                            onclick="confirm('Tolak tiket ini?') || event.stopImmediatePropagation()">
                                            <i class="fi fi-rr-cross"></i> Tolak
                                        </button>
                                    @else
                                        <span class="text-muted small">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Belum ada tiket.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer bg-white">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <!-- Approve Modal -->
    @if($showApproveModal && $selectedTicket)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeApproveModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fi fi-rr-check-circle fs-1 text-success"></i>
                            </div>
                            <h5>Anda yakin ingin menyetujui tiket ini?</h5>
                            <p class="text-muted">Tindakan ini akan dieksekusi secara otomatis oleh sistem.</p>
                        </div>

                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="card-title fw-bold text-dark mb-3">Detail Permintaan:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" width="40%">Pemohon</td>
                                                <td class="fw-bold">{{ $selectedTicket->user->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Asal Sekolah</td>
                                                <td class="fw-bold">{{ $selectedTicket->user->sekolah->nama ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tipe</td>
                                                <td>
                                                    @if($selectedTicket->type == 'delete_pendaftaran')
                                                        <span class="badge bg-danger">Hapus Pendaftaran</span>
                                                    @elseif($selectedTicket->type == 'reset_password')
                                                        <span class="badge bg-warning text-dark">Reset Password</span>
                                                    @elseif($selectedTicket->type == 'input_sekolah_dasar')
                                                        <span class="badge bg-info text-dark">Input Sekolah Dasar</span>
                                                    @elseif($selectedTicket->type == 'move_jalur')
                                                        <span class="badge bg-primary">Pindah Jalur</span>
                                                    @elseif($selectedTicket->type == 'unverify')
                                                        <span class="badge bg-danger">Buka Kunci Verifikasi</span>
                                                    @elseif($selectedTicket->type == 'correction_data')
                                                        <span class="badge bg-warning text-dark">Koreksi Data</span>
                                                    @elseif($selectedTicket->type == 'restore_pendaftaran')
                                                        <span class="badge bg-secondary">Restore Data</span>
                                                    @elseif($selectedTicket->type == 'delete_file')
                                                        <span class="badge bg-danger">Hapus Berkas</span>
                                                    @elseif($selectedTicket->type == 'transfer_school')
                                                        <span class="badge bg-info text-dark">Pindah Sekolah</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $selectedTicket->type }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small">
                                            @foreach($selectedTicket->payload as $key => $val)
                                                @if($key !== 'reason' && $key !== 'user_id')
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                        <span class="fw-bold text-end text-break">{{ $val }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                @if(isset($selectedTicket->payload['reason']))
                                    <div class="mt-3">
                                        <span class="text-muted d-block mb-1 small">Alasan Pengajuan:</span>
                                        <div class="p-3 bg-white border rounded fst-italic">
                                            "{{ $selectedTicket->payload['reason'] }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fi fi-rr-info me-2 fs-4"></i>
                            <div>
                                <strong>Perhatian:</strong>
                                @if($selectedTicket->type == 'delete_pendaftaran')
                                    Data Pendaftaran dan Berkas akan dihapus permanen.
                                @elseif($selectedTicket->type == 'reset_password')
                                    Password user akan direset menjadi 'password123'.
                                @elseif($selectedTicket->type == 'input_sekolah_dasar')
                                    Data Sekolah Dasar baru akan ditambahkan ke master data.
                                @elseif($selectedTicket->type == 'move_jalur')
                                    Jalur pendaftaran siswa akan diubah dan status dikembalikan ke 'process'.
                                @elseif($selectedTicket->type == 'unverify')
                                    Status pendaftaran akan di-reset menjadi 'process' agar bisa diedit kembali.
                                @elseif($selectedTicket->type == 'correction_data')
                                    Data Peserta Didik (NIK/NISN/Ibu) akan diperbarui secara permanen.
                                @elseif($selectedTicket->type == 'restore_pendaftaran')
                                    Data Pendaftaran yang terhapus akan dikembalikan (Restore).
                                @elseif($selectedTicket->type == 'delete_file')
                                    Berkas: <strong>{{ $selectedTicket->payload['nama_berkas'] ?? '-' }}</strong><br>
                                    <small>{{ $selectedTicket->payload['nama_file_asli'] ?? '' }}</small><br>
                                    Berkas ini akan dihapus permanen dari sistem.
                                @elseif($selectedTicket->type == 'transfer_school')
                                    <strong>Pindah ke: </strong> {{ $selectedTicket->payload['new_school_name'] }}<br>
                                    <small>Dari: {{ $selectedTicket->payload['old_school_name'] }}</small><br>
                                    Data pendaftaran akan dipindahkan ke sekolah baru dan status di-reset.
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeApproveModal">Batal</button>
                        <button type="button" class="btn btn-success" wire:click="approve">
                            <i class="fi fi-rr-check me-2"></i> Ya, Eksekusi Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>