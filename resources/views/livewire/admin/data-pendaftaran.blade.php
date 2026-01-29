<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Pendaftaran</h4>
            <p class="text-muted mb-0">Kelola pendaftaran peserta didik baru.</p>
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
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari nama, NISN, atau nomor pendaftaran..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="verified">Verified</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterJalur">
                        <option value="">Semua Jalur</option>
                        @foreach($jalurList as $jalur)
                            <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterSekolah">
                        <option value="">Semua Sekolah Tujuan</option>
                        @foreach($sekolahList as $sekolah)
                            <option value="{{ $sekolah->sekolah_id }}">{{ $sekolah->nama }}</option>
                        @endforeach
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
                            <th>No. Pendaftaran</th>
                            <th>Nama Peserta Didik</th>
                            <th>NISN</th>
                            <th>Sekolah Asal</th>
                            <th>Sekolah Tujuan</th>
                            <th>Jalur</th>
                            <th>Jarak</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftarans as $pendaftaran)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $pendaftaran->nomor_pendaftaran ?? '-' }}</span>
                                </td>
                                <td>{{ $pendaftaran->pesertaDidik->nama ?? '-' }}</td>
                                <td>{{ $pendaftaran->pesertaDidik->nisn ?? '-' }}</td>
                                <td>{{ $pendaftaran->pesertaDidik->sekolah->nama ?? '-' }}</td>
                                <td>{{ $pendaftaran->sekolah->nama ?? '-' }}</td>
                                <td>{{ $pendaftaran->jalur->nama ?? '-' }}</td>
                                <td>
                                    @if($pendaftaran->jarak_meter)
                                        {{ number_format($pendaftaran->jarak_meter / 1000, 2) }} KM
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'submitted' => 'primary',
                                            'verified' => 'info',
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$pendaftaran->status] ?? 'secondary' }}">
                                        {{ ucfirst($pendaftaran->status) }}
                                    </span>
                                </td>
                                <td>{{ $pendaftaran->tanggal_daftar?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        wire:click="showDetail('{{ $pendaftaran->id }}')">
                                        <i class="fi fi-rr-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary"
                                        wire:click="openStatusModal('{{ $pendaftaran->id }}')">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data pendaftaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pendaftarans->hasPages())
            <div class="card-footer bg-white">
                {{ $pendaftarans->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedPendaftaran)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Pendaftaran</h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-4">
                            <!-- Student Info -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Peserta Didik</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" width="40%">Nama</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->pesertaDidik->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">NISN</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->pesertaDidik->nisn ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sekolah Asal</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->pesertaDidik->sekolah->nama ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tempat, Tgl Lahir</td>
                                        <td class="fw-bold">
                                            {{ $selectedPendaftaran->pesertaDidik->tempat_lahir ?? '-' }},
                                            {{ $selectedPendaftaran->pesertaDidik->tanggal_lahir?->format('d/m/Y') ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Alamat</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->pesertaDidik->alamat_jalan ?? '-' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Registration Info -->
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Pendaftaran</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" width="40%">No. Pendaftaran</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->nomor_pendaftaran ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sekolah Tujuan</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->sekolah->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jalur</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->jalur->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jarak</td>
                                        <td class="fw-bold">
                                            @if($selectedPendaftaran->jarak_meter)
                                                {{ number_format($selectedPendaftaran->jarak_meter / 1000, 2) }} KM
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $statusColors[$selectedPendaftaran->status] ?? 'secondary' }}">
                                                {{ ucfirst($selectedPendaftaran->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tanggal Daftar</td>
                                        <td class="fw-bold">
                                            {{ $selectedPendaftaran->tanggal_daftar?->format('d F Y') ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Uploaded Files -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Berkas yang Diunggah</h6>
                                @if($selectedPendaftaran->berkas->count() > 0)
                                    <div class="list-group">
                                        @foreach($selectedPendaftaran->berkas as $file)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fi fi-rr-file me-2"></i>
                                                    {{ $file->berkas->nama ?? 'Berkas' }} -
                                                    <small class="text-muted">{{ $file->nama_file_asli }}</small>
                                                </div>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fi fi-rr-download"></i> Lihat
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted text-center py-3">Tidak ada berkas yang diunggah.</div>
                                @endif
                            </div>

                            <!-- Notes -->
                            @if($selectedPendaftaran->catatan)
                                <div class="col-12">
                                    <h6 class="text-muted mb-2">Catatan</h6>
                                    <div class="alert alert-warning mb-0">
                                        {{ $selectedPendaftaran->catatan }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Status Update Modal -->
    @if($showStatusModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status Pendaftaran</h5>
                        <button type="button" class="btn-close" wire:click="closeStatusModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select class="form-select" wire:model="newStatus">
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="verified">Verified</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            @error('newStatus') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" rows="3" wire:model="catatan"
                                placeholder="Masukkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeStatusModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="updateStatus">
                            <i class="fi fi-rr-check me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>