<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Daya Tampung</h4>
            <p class="text-muted mb-0">Kelola kuota penerimaan dan jumlah rombongan belajar sekolah Anda.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fi fi-rr-chart-pie-alt fs-4"></i>
                    </div>
                    <h2 class="mb-1">{{ number_format($statistics['total_daya_tampung']) }}</h2>
                    <p class="text-muted mb-0">Total Daya Tampung</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fi fi-rr-users-alt fs-4"></i>
                    </div>
                    <h2 class="mb-1">{{ number_format($statistics['total_terdaftar']) }}</h2>
                    <p class="text-muted mb-0">Total Terdaftar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fi fi-rr-hourglass-end fs-4"></i>
                    </div>
                    <h2 class="mb-1">{{ number_format($statistics['total_sisa']) }}</h2>
                    <p class="text-muted mb-0">Sisa Kuota</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistics per Jalur -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fi fi-rr-stats me-2"></i>Statistik Per Jalur Pendaftaran</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Jalur Pendaftaran</th>
                                    <th class="text-center">Kuota (%)</th>
                                    <th class="text-center">Kuota (Slot)</th>
                                    <th class="text-center">Terdaftar</th>
                                    <th class="text-center">Sisa</th>
                                    <th style="width: 150px;">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statistics['jalurs'] as $jalur)
                                    <tr>
                                        <td class="fw-medium">{{ $jalur['nama'] }}</td>
                                        <td class="text-center">{{ $jalur['kuota_persen'] }}%</td>
                                        <td class="text-center">{{ $jalur['kuota_slot'] }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-success-subtle text-success">{{ $jalur['terdaftar'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $jalur['sisa'] > 0 ? 'warning' : 'danger' }}-subtle text-{{ $jalur['sisa'] > 0 ? 'warning' : 'danger' }}">
                                                {{ $jalur['sisa'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar {{ $jalur['percentage'] >= 100 ? 'bg-danger' : ($jalur['percentage'] >= 75 ? 'bg-warning' : 'bg-success') }}"
                                                    role="progressbar" style="width: {{ $jalur['percentage'] }}%"
                                                    aria-valuenow="{{ $jalur['percentage'] }}" aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $jalur['percentage'] }}%</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Belum ada jalur pendaftaran aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="fi fi-rr-settings me-2"></i>Pengaturan Kapasitas</h5>
                </div>
                <div class="card-body">
                    @if($sekolah->is_locked_daya_tampung)
                        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                            <i class="fi fi-rr-lock me-2 fs-5"></i>
                            <div>
                                <strong>Data Terkunci</strong>
                                <div class="small">Daya tampung telah dikunci oleh dinas. Anda tidak dapat mengubah data
                                    ini.</div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="update">
                        @error('locked') <div class="alert alert-danger mb-3">{{ $message }}</div> @enderror

                        <div class="mb-3 p-3 border rounded bg-light">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="fi fi-rr-school"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $sekolah->nama }}</h6>
                                    <small class="text-muted">{{ $sekolah->npsn }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Rombel</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fi fi-rr-users-alt"></i></span>
                                <input type="number"
                                    class="form-control @error('form.jumlah_rombel') is-invalid @enderror"
                                    wire:model="form.jumlah_rombel" min="0" {{ $sekolah->is_locked_daya_tampung ? 'disabled' : '' }}>
                            </div>
                            @error('form.jumlah_rombel') <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Total Daya Tampung (Siswa)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fi fi-rr-chart-pie-alt"></i></span>
                                <input type="number"
                                    class="form-control @error('form.daya_tampung') is-invalid @enderror"
                                    wire:model="form.daya_tampung" min="0" {{ $sekolah->is_locked_daya_tampung ? 'disabled' : '' }}>
                            </div>
                            @error('form.daya_tampung') <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Total kuota siswa yang akan diterima (semua jalur).</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" {{ $sekolah->is_locked_daya_tampung ? 'disabled' : '' }}>
                                <i class="fi fi-rr-disk me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>