<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Daya Tampung</h4>
            <p class="text-muted mb-0">Kelola kuota dan jumlah rombongan belajar setiap sekolah.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-danger" onclick="confirmLockAll()">
                <i class="fi fi-rr-lock me-1"></i> Kunci Semua
            </button>
            <button class="btn btn-success" onclick="confirmUnlockAll()">
                <i class="fi fi-rr-unlock me-1"></i> Buka Semua
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fi fi-rr-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Sekolah Sudah Mengisi</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($sudahIsi) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fi fi-rr-exclamation fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Sekolah Belum Mengisi</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($belumIsi) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fi fi-rr-chair-office fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Total Daya Tampung</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($totalDayaTampung) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="row align-items-center g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari sekolah..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="sudah">Sudah Mengisi</option>
                        <option value="belum">Belum Mengisi</option>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <span class="text-muted">Total: {{ $sekolahs->total() }} Sekolah</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>NPSN</th>
                            <th>Nama Sekolah</th>
                            <th class="text-center">Jumlah Rombel</th>
                            <th class="text-center">Total Daya Tampung</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sekolahs as $sekolah)
                            <tr>
                                <td><code>{{ $sekolah->npsn }}</code></td>
                                <td class="fw-medium">{{ $sekolah->nama }}</td>
                                <td class="text-center">{{ $sekolah->jumlah_rombel }}</td>
                                <td class="text-center fw-bold">{{ $sekolah->daya_tampung }}</td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-sm btn-{{ $sekolah->is_locked_daya_tampung ? 'danger' : 'outline-warning' }}"
                                        wire:click="toggleLockDataTampung('{{ $sekolah->sekolah_id }}')"
                                        title="{{ $sekolah->is_locked_daya_tampung ? 'Buka Kunci Daya Tampung' : 'Kunci Daya Tampung' }}">
                                        <i class="fi fi-rr-{{ $sekolah->is_locked_daya_tampung ? 'lock' : 'unlock' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" wire:click="edit('{{ $sekolah->sekolah_id }}')">
                                        <i class="fi fi-rr-edit me-1"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada data sekolah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sekolahs->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $sekolahs->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Daya Tampung</h5>
                        <button type="button" class="btn-close" wire:click="closeEdit"></button>
                    </div>
                    <form wire:submit.prevent="update">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nama Sekolah</label>
                                <p class="fw-bold fs-5">{{ $form['nama'] }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Rombel</label>
                                <input type="number" class="form-control @error('form.jumlah_rombel') is-invalid @enderror"
                                    wire:model="form.jumlah_rombel" min="0">
                                @error('form.jumlah_rombel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Daya Tampung (Siswa)</label>
                                <input type="number" class="form-control @error('form.daya_tampung') is-invalid @enderror"
                                    wire:model="form.daya_tampung" min="0">
                                @error('form.daya_tampung') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Total kuota siswa yang diterima untuk sekolah ini.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" wire:click="closeEdit">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmLockAll() {
                Swal.fire({
                    title: 'Kunci Semua Data?',
                    text: "Seluruh sekolah tidak akan bisa mengubah daya tampung mereka.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Kunci Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('lockAll');
                    }
                });
            }

            function confirmUnlockAll() {
                Swal.fire({
                    title: 'Buka Semua Kunci?',
                    text: "Seluruh sekolah akan dapat mengubah daya tampung kembali.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Buka Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('unlockAll');
                    }
                });
            }
        </script>
    @endpush
</div>