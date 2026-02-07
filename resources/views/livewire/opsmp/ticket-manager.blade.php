<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Tiket Bantuan</h4>
            <p class="text-muted mb-0">Ajukan permintaan bantuan ke Admin (Hapus Data, Reset Password, dll).</p>
        </div>
        <button class="btn btn-primary" wire:click="openCreateModal">
            <i class="fi fi-rr-add me-2"></i> Buat Tiket
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tipe</th>
                            <th>Detail / Payload</th>
                            <th>Status</th>
                            <th>Catatan Admin</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>#{{ $ticket->id }}</td>
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
                                <td>
                                    <small class="d-block text-muted">
                                        @foreach($ticket->payload as $key => $val)
                                            @if($key !== 'reason' && $key !== 'user_id')
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}<br>
                                            @endif
                                        @endforeach
                                        @if(isset($ticket->payload['reason']) && $ticket->payload['reason'])
                                            <div class="mt-1 text-dark fst-italic">"{{ $ticket->payload['reason'] }}"</div>
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
                                <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-ticket fs-1 d-block mb-2"></i>
                                    Belum ada tiket bantuan.
                                </td>
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

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Tiket Bantuan</h5>
                        <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis Permintaan</label>
                            <select class="form-select" wire:model.live="type">
                                @foreach($this->availableTicketTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($type == 'delete_pendaftaran' || $type == 'reset_password' || $type == 'move_jalur' || $type == 'unverify' || $type == 'correction_data' || $type == 'restore_pendaftaran' || $type == 'delete_file' || $type == 'transfer_school')
                            <div class="mb-3">
                                <label class="form-label">Cari Siswa</label>
                                <input type="text" class="form-control" placeholder="Ketik Nama atau NISN..."
                                    wire:model.live.debounce.300ms="searchSiswa">
                                @if($type == 'restore_pendaftaran')
                                    <div class="form-text text-info"><i class="fi fi-rr-info"></i> Mencari data yang sudah dihapus
                                        (Trash).</div>
                                @endif
                                @if(!empty($searchResults))
                                    <div class="list-group mt-2">
                                        @foreach($searchResults as $result)
                                            <button type="button" class="list-group-item list-group-item-action"
                                                wire:click="selectPendaftaran('{{ $result->id }}', '{{ addslashes($result->pesertaDidik->nama) }}')">
                                                {{ $result->pesertaDidik->nama }} ({{ $result->pesertaDidik->nisn }})
                                                <br><small class="text-muted">{{ $result->nomor_pendaftaran }}</small>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                                @if($pendaftaran_id)
                                    <div class="mt-2 text-success">
                                        <i class="fi fi-rr-check me-1"></i> Terpilih: <strong>{{ $searchSiswa }}</strong>
                                    </div>
                                @endif
                                @error('pendaftaran_id') <div class="text-danger small mt-1">Wajib memilih siswa.</div>
                                @enderror
                            </div>
                        @endif

                        @if($type == 'move_jalur' && $pendaftaran_id)
                            <div class="mb-3">
                                <label class="form-label">Pilih Jalur Baru</label>
                                <select class="form-select" wire:model="input_jalur_id">
                                    <option value="">-- Pilih Jalur --</option>
                                    @foreach($this->jalurs as $jalur)
                                        <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                                    @endforeach
                                </select>
                                @error('input_jalur_id') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if($type == 'correction_data' && $pendaftaran_id)
                            <div class="alert alert-info py-2 small">
                                <i class="fi fi-rr-info me-1"></i> Isi hanya data yang ingin diubah. Data lain kosongkan.
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Koreksi NIK</label>
                                <input type="number" class="form-control" wire:model="input_nik"
                                    placeholder="Kosongkan jika tidak ubah">
                                @error('input_nik') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Koreksi NISN</label>
                                <input type="number" class="form-control" wire:model="input_nisn"
                                    placeholder="Kosongkan jika tidak ubah">
                                @error('input_nisn') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Koreksi Nama Ibu Kandung</label>
                                <input type="text" class="form-control" wire:model="input_nama_ibu"
                                    placeholder="Kosongkan jika tidak ubah">
                                @error('input_nama_ibu') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if($type == 'delete_file' && $pendaftaran_id)
                            <div class="mb-3">
                                <label class="form-label">Pilih Berkas yang akan dihapus</label>
                                <select class="form-select" wire:model="input_berkas_id">
                                    <option value="">-- Pilih Berkas --</option>
                                    @forelse($this->uploadedFiles as $file)
                                        <option value="{{ $file->id }}">{{ $file->berkas->nama ?? 'Unknown' }}
                                            ({{ $file->nama_file_asli }})</option>
                                    @empty
                                        <option disabled>Tidak ada berkas yang diupload</option>
                                    @endforelse
                                </select>
                                @error('input_berkas_id') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if($type == 'transfer_school')
                            <div class="mb-3">
                                <label class="form-label">Pilih Sekolah Tujuan (SMP)</label>
                                <select class="form-select" wire:model="input_sekolah_id">
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach($this->availableSchools as $sekolah)
                                        <option value="{{ $sekolah->id }}">{{ $sekolah->nama }}</option>
                                    @endforeach
                                </select>
                                @error('input_sekolah_id') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if($type == 'input_sekolah_dasar')
                            <div class="mb-3">
                                <label class="form-label">NPSN</label>
                                <input type="text" class="form-control" wire:model="input_npsn">
                                @error('input_npsn') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Sekolah</label>
                                <input type="text" class="form-control" wire:model="input_nama_sekolah">
                                @error('input_nama_sekolah') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat Jalan</label>
                                <input type="text" class="form-control" wire:model="input_alamat">
                                @error('input_alamat') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Alasan / Catatan</label>
                            <textarea class="form-control" rows="3" wire:model="catatan"
                                placeholder="Jelaskan kenapa Anda mengajukan ini..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="store">
                            <i class="fi fi-rr-paper-plane me-2"></i> Kirim Tiket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>