<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Pendaftaran</h4>
            <p class="text-muted mb-0">Daftar siswa yang mendaftar ke sekolah Anda.</p>
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
                    <input type="text" class="form-control" placeholder="Cari nama, NISN, atau nomor pendaftaran..."
                        wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="verified">Verified</option>
                        <option value="accepted">Accepted</option>
                        <option value="rejected">Rejected</option>
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
                            <th>Jalur</th>
                            <th>Jarak</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftarans as $pendaftaran)
                            <tr>
                                <td><span class="fw-medium">{{ $pendaftaran->nomor_pendaftaran ?? '-' }}</span></td>
                                <td>{{ $pendaftaran->pesertaDidik->nama ?? '-' }}</td>
                                <td>{{ $pendaftaran->pesertaDidik->nisn ?? '-' }}</td>
                                <td>{{ $pendaftaran->pesertaDidik->sekolah->nama ?? '-' }}</td>
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
                                    <a href="{{ route('opsmp.verval-berkas-detail', $pendaftaran->id) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="fi fi-rr-file-check"></i> Verval
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-inbox fs-1 d-block mb-2"></i>
                                    Belum ada pendaftaran.
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
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Data Pendaftaran</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" width="40%">No. Pendaftaran</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->nomor_pendaftaran ?? '-' }}</td>
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
                                </table>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Berkas yang Diunggah</h6>
                                @if($selectedPendaftaran->berkas->count() > 0)
                                    <div class="list-group">
                                        @foreach($selectedPendaftaran->berkas as $file)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fi fi-rr-file me-2"></i>
                                                    {{ $file->berkas->nama ?? 'Berkas' }}
                                                </div>
                                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fi fi-rr-eye"></i> Lihat
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted text-center py-3">Tidak ada berkas.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>