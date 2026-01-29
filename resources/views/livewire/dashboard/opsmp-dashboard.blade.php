<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Dashboard Operator SMP</h4>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Coming Soon Card -->
    <div class="row">
        <div class="col-lg-8">
            @if($sekolah)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-lg bg-success bg-opacity-10 text-success rounded-circle me-3">
                                <i class="fi fi-rr-building" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $sekolah->nama }}</h5>
                                <div class="d-flex gap-2">
                                    <span
                                        class="badge {{ $sekolah->status_sekolah == 'Negeri' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $sekolah->status_sekolah }}
                                    </span>
                                    <span class="badge bg-secondary font-monospace">{{ $sekolah->npsn }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Kode Wilayah</label>
                                <p class="fw-medium mb-0">{{ $sekolah->kode_wilayah ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Bentuk Pendidikan</label>
                                <p class="fw-medium mb-0">{{ $sekolah->bentuk_pendidikan_id ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small mb-1">Alamat Lengkap</label>
                                <p class="fw-medium mb-0">{{ $sekolah->alamat_lengkap }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="fi fi-rr-exclamation ms-2 display-6 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Belum Terhubung ke Sekolah</h5>
                        <p class="mb-0">Akun Anda belum dihubungkan dengan data sekolah manapun. Hubungi Administrator.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Sidebar widgets or stats can go here -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">Statistik PPDB</h6>
                    <!-- Placeholder stats -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Pendaftar</span>
                        <span class="badge bg-primary rounded-pill">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Kuota Tersedia</span>
                        <span class="badge bg-success rounded-pill">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>