<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Dashboard Operator SMP</h4>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    @if($sekolah)
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block text-muted text-uppercase fw-medium small mb-1">Total Pendaftar</span>
                                <h3 class="mb-0 fw-bold">{{ number_format($this->stats['total_pendaftar'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar bg-primary bg-opacity-10 text-primary rounded">
                                <i class="fi fi-rr-users-alt fs-4"></i>
                            </div>
                        </div>
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary">+{{ $this->dailyRegistrations->last()['count'] ?? 0 }}
                            Hari Ini</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block text-muted text-uppercase fw-medium small mb-1">Terverifikasi</span>
                                <h3 class="mb-0 fw-bold">{{ number_format($this->stats['total_verified'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar bg-success bg-opacity-10 text-success rounded">
                                <i class="fi fi-rr-check-circle fs-4"></i>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $this->stats['percentage_filled'] ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">{{ $this->stats['percentage_filled'] ?? 0 }}% dari
                            Kuota</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block text-muted text-uppercase fw-medium small mb-1">Total Kuota</span>
                                <h3 class="mb-0 fw-bold">{{ number_format($this->stats['total_daya_tampung'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar bg-info bg-opacity-10 text-info rounded">
                                <i class="fi fi-rr-chart-pie-alt fs-4"></i>
                            </div>
                        </div>
                        <small class="text-muted">Total Daya Tampung Sekolah</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block text-muted text-uppercase fw-medium small mb-1">Sisa Kuota</span>
                                <h3 class="mb-0 fw-bold">{{ number_format($this->stats['sisa_kuota'] ?? 0) }}</h3>
                            </div>
                            <div class="avatar bg-warning bg-opacity-10 text-warning rounded">
                                <i class="fi fi-rr-time-fast fs-4"></i>
                            </div>
                        </div>
                        <small class="text-muted">Kuota tersisa (verified only)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Charts -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-0 bg-transparent py-3">
                        <h6 class="mb-0 fw-bold">Statistik Pendaftaran (7 Hari Terakhir)</h6>
                    </div>
                    <div class="card-body">
                        <div id="registrationChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-0 bg-transparent py-3">
                        <h6 class="mb-0 fw-bold">Kuota per Jalur</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($this->jalurStats as $jalur)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-medium">{{ $jalur['nama'] }}</span>
                                        <span class="small text-muted">{{ $jalur['verified'] }} / {{ $jalur['kuota'] }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ $jalur['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Pendaftaran Terbaru</h6>
                <a href="{{ route('opsmp.pendaftaran') }}" class="btn btn-sm btn-light">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Nama Siswa</th>
                            <th>NISN</th>
                            <th>Jalur</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->recentRegistrations as $reg)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                            {{ strtoupper(substr($reg->pesertaDidik->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $reg->pesertaDidik->nama }}</div>
                                            <small
                                                class="text-muted">{{ $reg->pesertaDidik->sekolahDasar->nama ?? 'SD Asal' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $reg->pesertaDidik->nisn }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $reg->jalur->nama }}</span></td>
                                <td>
                                    @if($reg->status == 'verified')
                                        <span class="badge bg-success">Verified</span>
                                    @elseif($reg->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Proses</span>
                                    @endif
                                </td>
                                <td>{{ $reg->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pendaftaran masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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

@push('scripts')
    <script src="{{ asset('templates/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            var options = {
                series: [{
                    name: 'Pendaftar',
                    data: @json($this->dailyRegistrations->pluck('count'))
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: { show: false },
                    fontFamily: 'Plus Jakarta Sans',
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: @json($this->dailyRegistrations->pluck('date')),
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { show: false },
                grid: {
                    strokeDashArray: 4,
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                colors: ['#0d6efd'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#registrationChart"), options);
            chart.render();
        });
    </script>
@endpush