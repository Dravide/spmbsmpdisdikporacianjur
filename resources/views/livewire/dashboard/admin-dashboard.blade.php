<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Dashboard Administrator</h4>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
        <div class="text-muted">
            <i class="fi fi-rr-calendar me-1"></i>{{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Pendaftar -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="mb-0 fw-bold">{{ number_format($registrationStats['total']) }}</h2>
                            <span class="opacity-75">Total Pendaftar</span>
                        </div>
                        <div class="opacity-50">
                            <i class="fi fi-rr-users fs-1"></i>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top border-white border-opacity-25">
                        <small class="opacity-75">
                            <i class="fi fi-rr-calendar-day me-1"></i>Hari ini: {{ $registrationStats['today'] }}
                            @if($registrationStats['percentChange'] != 0)
                                <span
                                    class="ms-2 {{ $registrationStats['percentChange'] > 0 ? 'text-success-light' : 'text-danger-light' }}">
                                    <i
                                        class="fi {{ $registrationStats['percentChange'] > 0 ? 'fi-rr-arrow-up' : 'fi-rr-arrow-down' }}"></i>
                                    {{ abs($registrationStats['percentChange']) }}%
                                </span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Peserta Didik -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-info bg-opacity-10 text-info rounded-circle">
                            <i class="fi fi-rr-student"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($totalPesertaDidik) }}</h3>
                            <small class="text-muted">Total Peserta Didik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Sekolah -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-success bg-opacity-10 text-success rounded-circle">
                            <i class="fi fi-rr-school"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($totalSekolahSMP) }}</h3>
                            <small class="text-muted">Sekolah SMP</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jalur -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-warning bg-opacity-10 text-warning rounded-circle">
                            <i class="fi fi-rr-road"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ number_format($totalJalur) }}</h3>
                            <small class="text-muted">Jalur Pendaftaran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Daily Registrations Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fi fi-rr-chart-line-up me-2 text-primary"></i>Pendaftaran 7 Hari Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jalur Donut Chart -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-chart-pie me-2 text-primary"></i>Pendaftar per Jalur</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if(count($registrationByJalur) > 0)
                        <div style="height: 250px; width: 100%; position: relative;">
                            <canvas id="jalurChart"></canvas>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fi fi-rr-chart-pie fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Top Schools Row -->
    <div class="row g-3 mb-4">
        <!-- Status Breakdown -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-list-check me-2 text-primary"></i>Status Pendaftaran</h6>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'verified' => 'info',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu',
                            'verified' => 'Terverifikasi',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                            'diterima' => 'Diterima',
                            'ditolak' => 'Ditolak',
                        ];
                    @endphp
                    @forelse($statusBreakdown as $status => $count)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} me-2">{{ $count }}</span>
                                <span>{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                            </div>
                            <div class="progress flex-grow-1 mx-3" style="height: 6px; max-width: 100px;">
                                <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}"
                                    style="width: {{ $registrationStats['total'] > 0 ? ($count / $registrationStats['total']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fi fi-rr-document fs-1 opacity-50"></i>
                            <p class="mb-0 mt-2">Belum ada data</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Schools -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-trophy me-2 text-primary"></i>Sekolah Terpopuler</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Nama Sekolah</th>
                                    <th class="border-0 text-end">Pendaftar</th>
                                    <th class="border-0" style="width: 30%;">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rank = 1;
                                $maxCount = count($topSchools) > 0 ? max($topSchools) : 1; @endphp
                                @forelse($topSchools as $sekolah => $count)
                                    <tr>
                                        <td>
                                            @if($rank <= 3)
                                                <span
                                                    class="badge bg-{{ $rank == 1 ? 'warning' : ($rank == 2 ? 'secondary' : 'danger') }}">
                                                    {{ $rank }}
                                                </span>
                                            @else
                                                {{ $rank }}
                                            @endif
                                        </td>
                                        <td>{{ $sekolah }}</td>
                                        <td class="text-end fw-bold">{{ $count }}</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary"
                                                    style="width: {{ ($count / $maxCount) * 100 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $rank++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data pendaftaran</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats & Recent Logins -->
    <div class="row g-3">
        <!-- User Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-users me-2 text-primary"></i>Pengguna Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Total Pengguna</span>
                        <span class="fw-bold">{{ $totalUsers }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Pengguna Aktif</span>
                        <span class="fw-bold text-success">{{ $activeUsers }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Sesi Aktif</span>
                        <span class="fw-bold text-info">{{ $activeSessions }}</span>
                    </div>
                    <hr>
                    <h6 class="text-muted mb-3">Per Role</h6>
                    @foreach($roleSettings as $setting)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $setting->role_label }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $usersByRole[$setting->role] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Logins -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fi fi-rr-clock me-2 text-primary"></i>Login Terakhir</h6>
                    <a href="{{ route('sessions') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Pengguna</th>
                                    <th class="border-0">IP Address</th>
                                    <th class="border-0">Perangkat</th>
                                    <th class="border-0">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogins as $session)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs bg-primary text-white rounded-circle">
                                                    {{ strtoupper(substr($session->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div class="ms-2">
                                                    <span class="d-block">{{ $session->user->name ?? '-' }}</span>
                                                    <small
                                                        class="text-muted">{{ $session->user->role_label ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code>{{ $session->ip_address }}</code></td>
                                        <td>{{ $session->device_name }}</td>
                                        <td>{{ $session->last_activity->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada login</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Daily Registrations Chart
            const dailyCtx = document.getElementById('dailyChart');
            if (dailyCtx) {
                new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: @json(array_column($dailyRegistrations, 'date')),
                        datasets: [{
                            label: 'Pendaftar',
                            data: @json(array_column($dailyRegistrations, 'count')),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            // Jalur Donut Chart
            const jalurCtx = document.getElementById('jalurChart');
            if (jalurCtx) {
                const jalurData = @json($registrationByJalur);
                const jalurLabels = Object.keys(jalurData);
                const jalurValues = Object.values(jalurData);

                if (jalurLabels.length > 0) {
                    new Chart(jalurCtx, {
                        type: 'doughnut',
                        data: {
                            labels: jalurLabels,
                            datasets: [{
                                data: jalurValues,
                                backgroundColor: [
                                    '#667eea',
                                    '#28a745',
                                    '#ffc107',
                                    '#dc3545',
                                    '#17a2b8',
                                    '#6f42c1',
                                ],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
@endpush