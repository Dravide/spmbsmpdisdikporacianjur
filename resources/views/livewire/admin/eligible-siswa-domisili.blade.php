<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Eligible Siswa Domisili</h4>
            <p class="text-muted mb-0">Cek eligibilitas siswa untuk jalur pendaftaran domisili berdasarkan pemetaan
                zona.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Info Card -->
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="d-flex align-items-start">
            <i class="fi fi-rr-info fs-5 me-3 mt-1"></i>
            <div>
                <strong>Cara Kerja:</strong> Halaman ini menampilkan siswa yang memiliki data domisili lengkap dan
                mencocokkan dengan zona domisili sekolah yang sudah dipetakan. Klik "Cek Eligibilitas" untuk melihat
                sekolah mana saja yang dapat didaftari melalui jalur domisili.
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Cari nama atau NISN siswa..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $pendaftarans->total() }} Pendaftar dengan domisili</span>
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
                            <th>Sekolah Asal</th>
                            <th>Kecamatan</th>
                            <th>Desa/Kelurahan</th>
                            <th>RW</th>
                            <th>RT</th>
                            <th class="text-center">Eligible</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendaftarans as $pendaftaran)
                            @php $siswa = $pendaftaran->pesertaDidik; @endphp
                            @if($siswa)
                                <tr>
                                    <td class="fw-medium">{{ $siswa->nama }}</td>
                                    <td><code>{{ $siswa->nisn }}</code></td>
                                    <td class="small text-muted">{{ $siswa->sekolah->nama ?? '-' }}</td>
                                    <td>{{ $siswa->kecamatan ?? '-' }}</td>
                                    <td>{{ $siswa->desa_kelurahan ?? '-' }}</td>
                                    <td>{{ $siswa->rw ?? '-' }}</td>
                                    <td>{{ $siswa->rt ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($pendaftaran->eligible_count > 0)
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $pendaftaran->eligible_count }} Sekolah
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                0 Sekolah
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="checkEligibility('{{ $siswa->id }}')">
                                            <i class="fi fi-rr-search me-1"></i> Cek
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-user-slash fs-1 d-block mb-2"></i>
                                    Tidak ada data pendaftaran dengan domisili lengkap.
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
    @if($showDetailModal && $selectedSiswa)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">Eligibilitas Jalur Domisili</h5>
                            <p class="text-muted mb-0 small">{{ $selectedSiswa->nama }} ({{ $selectedSiswa->nisn }})</p>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Student Domicile Info -->
                        <div class="alert alert-light border mb-4">
                            <h6 class="mb-2"><i class="fi fi-rr-marker me-1"></i> Data Domisili Siswa</h6>
                            <div class="row g-2 small">
                                <div class="col-md-3">
                                    <span class="text-muted">Kecamatan:</span><br>
                                    <strong>{{ $selectedSiswa->kecamatan ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted">Desa/Kelurahan:</span><br>
                                    <strong>{{ $selectedSiswa->desa_kelurahan ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted">RW:</span><br>
                                    <strong>{{ $selectedSiswa->rw ?? '-' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-muted">RT:</span><br>
                                    <strong>{{ $selectedSiswa->rt ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Eligible Schools -->
                        <h6 class="mb-3">
                            <i class="fi fi-rr-school me-1"></i> Sekolah yang Dapat Didaftari
                            ({{ count($eligibleSekolahList) }})
                        </h6>

                        @if(count($eligibleSekolahList) > 0)
                            <div class="list-group">
                                @foreach($eligibleSekolahList as $item)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $item['sekolah']['nama'] }}</h6>
                                                <small class="text-muted">NPSN: {{ $item['sekolah']['npsn'] }}</small>
                                            </div>
                                            <span class="badge bg-success">Eligible</span>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Zona yang cocok:</small>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($item['matching_zones'] as $zone)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $zone['kecamatan'] }}
                                                        @if($zone['desa']) - {{ $zone['desa'] }} @endif
                                                        @if($zone['rw']) RW {{ $zone['rw'] }} @endif
                                                        @if($zone['rt']) RT {{ $zone['rt'] }} @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fi fi-rr-sad fs-1 d-block mb-2"></i>
                                <p class="mb-1">Tidak ada sekolah yang eligible.</p>
                                <small>Pastikan data domisili siswa sesuai dengan zona yang sudah dipetakan di menu Pemetaan
                                    Domisili.</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>