<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Dashboard Administrator</h4>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="fi fi-rr-users"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $totalUsers }}</h3>
                            <small class="text-muted">Total Pengguna</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-success bg-opacity-10 text-success rounded-circle">
                            <i class="fi fi-rr-user-check"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $activeUsers }}</h3>
                            <small class="text-muted">Pengguna Aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-info bg-opacity-10 text-info rounded-circle">
                            <i class="fi fi-rr-shield-check"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $activeSessions }}</h3>
                            <small class="text-muted">Sesi Aktif</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-warning bg-opacity-10 text-warning rounded-circle">
                            <i class="fi fi-rr-graduation-cap"></i>
                        </div>
                        <div class="ms-3">
                            <h3 class="mb-0">{{ $usersByRole['cmb'] ?? 0 }}</h3>
                            <small class="text-muted">Calon Murid</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- User by Role -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0">Pengguna per Role</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($roleSettings as $setting)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>{{ $setting->role_label }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $usersByRole[$setting->role] ?? 0 }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Logins -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Login Terakhir</h6>
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
                                                    {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                                </div>
                                                <div class="ms-2">
                                                    <span class="d-block">{{ $session->user->name }}</span>
                                                    <small class="text-muted">{{ $session->user->role_label }}</small>
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