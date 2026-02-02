<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pengolahan Data</h4>
            <p class="text-muted mb-0">Rekapitulasi siswa yang telah diverifikasi berdasarkan jalur pendaftaran.</p>
        </div>
    </div>

    <!-- Jalur Cards -->
    <div class="row g-3">
        @foreach($jalurList as $jalur)
            <div class="col-md-3">
                <a href="{{ route('opsmp.jalur-verified.detail', $jalur->id) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm cursor-pointer hover-card" style="transition: all 0.2s;">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="fi fi-rr-road fs-1 text-primary"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $jalur->nama }}</h6>
                            <div class="fs-2 fw-bold text-success">{{ $jalur->pendaftarans_count }}</div>
                            <small class="text-muted">Siswa Terverifikasi</small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
    </style>
</div>