<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Administrator</h4>
            <p class="text-muted mb-0">Kelola akun administrator sistem.</p>
        </div>
        <button wire:click="create" class="btn btn-primary">
            <i class="fi fi-rr-plus me-2"></i>Tambah Admin
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-start-0"
                            placeholder="Cari nama, username, email..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 px-4">Nama Lengkap</th>
                            <th class="border-0 py-3 px-4">Username</th>
                            <th class="border-0 py-3 px-4">Email</th>
                            <th class="border-0 py-3 px-4">Status</th>
                            <th class="border-0 py-3 px-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="ms-3">
                                            <span class="d-block fw-medium">{{ $user->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4"><code>{{ $user->username }}</code></td>
                                <td class="px-4">{{ $user->email ?? '-' }}</td>
                                <td class="px-4">
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="px-4 text-end">
                                    <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-icon btn-light"
                                        title="Edit">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    @if($user->two_factor_secret)
                                        <button onclick="confirmReset2FA({{ $user->id }}, '{{ $user->name }}')"
                                            class="btn btn-sm btn-icon btn-light text-warning" title="Reset 2FA">
                                            <i class="fi fi-rr-shield-check"></i>
                                        </button>
                                    @endif

                                    @if(auth()->id() !== $user->id)
                                        <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')"
                                            class="btn btn-sm btn-icon btn-light text-danger" title="Hapus">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fi fi-rr-info mb-2 fs-3"></i>
                                        <span>Belum ada data administrator</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($users->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if ($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit Administrator' : 'Tambah Administrator' }}</h5>
                        <button type="button" class="btn-close" wire:click="cancelCreate"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                    wire:model="form.name" placeholder="Masukkan nama lengkap">
                                @error('form.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('form.username') is-invalid @enderror"
                                    wire:model="form.username" placeholder="Masukkan username login">
                                @error('form.username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email (Opsional)</label>
                                <input type="email" class="form-control @error('form.email') is-invalid @enderror"
                                    wire:model="form.email" placeholder="example@email.com">
                                @error('form.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Password
                                    @if ($isEditMode)
                                        <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" class="form-control @error('form.password') is-invalid @enderror"
                                    wire:model="form.password" placeholder="••••••••">
                                @error('form.password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light" wire:click="cancelCreate">Batal</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ $isEditMode ? 'Simpan Perubahan' : 'Tambahkan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Administrator?',
                html: `Anda akan menghapus administrator <strong>${name}</strong>?<br><small class="text-danger">Tindakan ini tidak dapat dibatalkan!</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.delete(id);
                }
            });
        }

        function confirmReset2FA(id, name) {
            Swal.fire({
                title: 'Reset 2FA?',
                html: `Anda akan mereset autentikasi dua faktor untuk <strong>${name}</strong>?<br><small class="text-muted">Pengguna harus melakukan setup ulang 2FA saat login berikutnya.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.resetTwoFactor(id);
                }
            });
        }

        // Listen for events used in this component
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('import-success', (event) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: event.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            Livewire.on('import-error', (event) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: event.message,
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
@endpush