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

    <!-- Toolbar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="input-group" style="max-width: 400px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fi fi-rr-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0"
                        placeholder="Cari nama, NISN, atau nomor pendaftaran..."
                        wire:model.live.debounce.500ms="search">
                </div>

                <button class="btn btn-outline-primary position-relative" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#filterOffcanvas">
                    <i class="fi fi-rr-settings-sliders me-2"></i> Filter Lanjutan
                    @php
                        $filterCount = 0;
                        if ($filterStatus)
                            $filterCount++;
                        if ($filterJalur)
                            $filterCount++;
                        if ($filterSekolah)
                            $filterCount++;
                        if ($startDate || $endDate)
                            $filterCount++;
                    @endphp
                    @if($filterCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $filterCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas Filters -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" wire:ignore.self data-bs-backdrop="true">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Filter Lanjutan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Status Pendaftaran</label>
                <select class="form-select" wire:model="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="verified">Verified</option>
                    <option value="accepted">Accepted</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Jalur Pendaftaran</label>
                <select class="form-select" wire:model="filterJalur">
                    <option value="">Semua Jalur</option>
                    @foreach($jalurList as $jalur)
                        <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Sekolah Asal (SD/MI)</label>
                <select class="form-select" wire:model="filterSekolah">
                    <option value="">Semua Sekolah</option>
                    @foreach($sekolahDasarList as $sd)
                        <option value="{{ $sd->sekolah_id }}">{{ $sd->nama }}</option>
                    @endforeach
                </select>
                <div class="form-text small">Hanya menampilkan sekolah dari pendaftar yang ada.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">Tanggal Daftar</label>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-light">Mulai</span>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-light">Sampai</span>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary" wire:click="applyFilters" data-bs-dismiss="offcanvas">
                    <i class="fi fi-rr-check me-2"></i> Terapkan
                </button>
                <button class="btn btn-outline-danger" wire:click="resetFilters" data-bs-dismiss="offcanvas">
                    <i class="fi fi-rr-rotate-right me-2"></i> Reset Filter
                </button>
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
                                        <div class="small text-muted">
                                            ({{ number_format($pendaftaran->jarak_meter, 0, ',', '.') }} m)</div>
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
                                <td>{{ $pendaftaran->tanggal_daftar?->translatedFormat('d F Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Aksi
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    wire:click.prevent="showDetail('{{ $pendaftaran->id }}')">
                                                    <i class="fi fi-rr-eye me-2"></i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('opsmp.verval-berkas-detail', $pendaftaran->id) }}">
                                                    <i class="fi fi-rr-file-check me-2"></i> Verval
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
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
                                        <td class="text-muted">Sekolah Tujuan 1</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->sekolah->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sekolah Tujuan 2</td>
                                        <td class="fw-bold">{{ $selectedPendaftaran->sekolah2->nama ?? '-' }}</td>
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
                                                <span
                                                    class="text-muted small">({{ number_format($selectedPendaftaran->jarak_meter, 0, ',', '.') }}
                                                    m)</span>
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