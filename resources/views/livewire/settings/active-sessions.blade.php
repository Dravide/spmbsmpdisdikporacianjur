<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Sesi Login Aktif</h4>
            <p class="text-muted mb-0">Pantau dan kelola sesi login pengguna.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Cari nama, username, atau IP..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Pengguna</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">IP Address</th>
                            <th class="border-0">Perangkat</th>
                            <th class="border-0">Aktivitas</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle">
                                            {{ strtoupper(substr($session->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-2">
                                            <span class="d-block fw-medium">{{ $session->user->name }}</span>
                                            <small class="text-muted">{{ $session->user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $session->user->role === 'admin' ? 'danger' : 'primary' }}">
                                        {{ $session->user->role_label }}
                                    </span>
                                </td>
                                <td><code>{{ $session->ip_address }}</code></td>
                                <td>{{ $session->device_name ?? 'Unknown' }}</td>
                                <td>
                                    <span title="{{ $session->last_activity->format('d M Y H:i:s') }}">
                                        {{ $session->last_activity->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmTerminate({{ $session->id }}, '{{ $session->user->name }}')">
                                        <i class="fi fi-rr-sign-out-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if($search)
                                        Tidak ada sesi ditemukan untuk "{{ $search }}"
                                    @else
                                        Tidak ada sesi aktif
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sessions->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmTerminate(sessionId, userName) {
            Swal.fire({
                title: 'Hentikan Sesi?',
                html: `Yakin ingin menghentikan sesi <strong>${userName}</strong>?<br><small class="text-muted">Pengguna akan otomatis logout.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hentikan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.terminateSession(sessionId);
                }
            });
        }

        // Listen for success event from Livewire
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('session-terminated', () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Sesi telah dihentikan.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    </script>
@endpush