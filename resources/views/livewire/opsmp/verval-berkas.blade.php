<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Verifikasi & Validasi Berkas</h4>
            <p class="text-muted mb-0">Verifikasi berkas yang diunggah oleh calon peserta didik.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" placeholder="Cari nama atau NISN siswa..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="revision">Perbaikan</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Siswa</th>
                            <th>NISN</th>
                            <th>Jenis Berkas</th>
                            <th>Nama File</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkasList as $item)
                            <tr>
                                <td>{{ $item->pendaftaran->pesertaDidik->nama ?? '-' }}</td>
                                <td>{{ $item->pendaftaran->pesertaDidik->nisn ?? '-' }}</td>
                                <td>{{ $item->berkas->nama ?? '-' }}</td>
                                <td>
                                    <small class="text-muted">{{ $item->nama_file_asli ?? '-' }}</small>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'secondary',
                                            'approved' => 'success',
                                            'revision' => 'warning',
                                            'rejected' => 'danger',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'approved' => 'Disetujui',
                                            'revision' => 'Perbaikan',
                                            'rejected' => 'Ditolak',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$item->status_berkas] ?? 'secondary' }}">
                                        {{ $statusLabels[$item->status_berkas] ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fi fi-rr-eye"></i>
                                    </a>
                                    @if($item->status_berkas !== 'approved')
                                        <button class="btn btn-sm btn-success" wire:click="quickApprove({{ $item->id }})"
                                            title="Setujui">
                                            <i class="fi fi-rr-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-outline-secondary" wire:click="openModal({{ $item->id }})"
                                        title="Ubah Status">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-inbox fs-1 d-block mb-2"></i>
                                    Belum ada berkas yang perlu diverifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($berkasList->hasPages())
            <div class="card-footer bg-white">
                {{ $berkasList->links() }}
            </div>
        @endif
    </div>

    <!-- Status Update Modal -->
    @if($showModal && $selectedBerkas)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status Berkas</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Siswa</label>
                            <div class="fw-bold">{{ $selectedBerkas->pendaftaran->pesertaDidik->nama ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Jenis Berkas</label>
                            <div class="fw-bold">{{ $selectedBerkas->berkas->nama ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $selectedBerkas->file_path) }}" target="_blank"
                                class="btn btn-outline-primary w-100">
                                <i class="fi fi-rr-eye me-2"></i> Lihat Berkas
                            </a>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select class="form-select" wire:model="newStatus">
                                <option value="pending">Pending</option>
                                <option value="approved">Disetujui</option>
                                <option value="revision">Perbaikan</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            @error('newStatus') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Wajib untuk Perbaikan/Ditolak)</label>
                            <textarea class="form-control" rows="3" wire:model="catatan"
                                placeholder="Masukkan alasan atau catatan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="updateStatus">
                            <i class="fi fi-rr-check me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>