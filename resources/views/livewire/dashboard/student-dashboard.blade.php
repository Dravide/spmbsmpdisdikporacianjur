<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Dashboard Siswa</h4>
            <p class="text-muted mb-0">Selamat datang, {{ $user->nama }}</p>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                         <div class="avatar avatar-xl rounded-circle bg-primary-subtle text-primary mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ substr($user->nama, 0, 1) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $user->nama }}</h5>
                    <p class="text-muted mb-3">{{ $user->nisn }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('print.kartu', $user->id) }}" target="_blank" class="btn btn-primary">
                            <i class="fi fi-rr-print me-2"></i> Cetak Kartu Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- School Info -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0">Informasi Sekolah</h5>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 140px;">Asal Sekolah</td>
                                    <td class="fw-medium">
                                        @php 
                                            $sekolah = \App\Models\SekolahDasar::where('sekolah_id', $user->sekolah_id)->first();
                                        @endphp
                                        {{ $sekolah->nama ?? 'Tidak Diketahui' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NPSN</td>
                                    <td class="fw-medium">{{ $sekolah->npsn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tempat Lahir</td>
                                    <td class="fw-medium">{{ $user->tempat_lahir }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Lahir</td>
                                    <td class="fw-medium">{{ $user->tanggal_lahir ? $user->tanggal_lahir->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama Ibu</td>
                                    <td class="fw-medium">{{ $user->nama_ibu_kandung ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
