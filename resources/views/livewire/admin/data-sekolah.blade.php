<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Sekolah Dasar</h4>
            <p class="text-muted mb-0">Kelola data sekolah dasar dan akun operator.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger" onclick="confirmReset()">
                <i class="fi fi-rr-trash me-1"></i> Reset Data
            </button>
            <button type="button" class="btn btn-success" wire:click="create">
                <i class="fi fi-rr-plus me-1"></i> Tambah Sekolah
            </button>
            <a href="{{ route('admin.sekolah-sd.import') }}" class="btn btn-primary" wire:navigate>
                <i class="fi fi-rr-file-import me-1"></i> Import Data
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0"
                            placeholder="Cari nama, NPSN, atau desa..." wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $sekolahList->total() }} sekolah</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">NPSN</th>
                            <th class="border-0">Nama Sekolah</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Desa/Kelurahan</th>
                            <th class="border-0">Akun</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sekolahList as $sekolah)
                            <tr>
                                <td><code>{{ $sekolah->npsn }}</code></td>
                                <td>
                                    <span class="fw-medium">{{ $sekolah->nama }}</span>
                                </td>
                                <td>
                                    @php
                                        $status = $sekolah->status_sekolah;
                                        $isNegeri = $status === 'Negeri' || $status == 1;
                                        $isSwasta = $status === 'Swasta' || $status == 2;
                                        $displayText = $isNegeri ? 'Negeri' : ($isSwasta ? 'Swasta' : ($status ?? '-'));
                                        $badgeClass = $isNegeri ? 'bg-success' : ($isSwasta ? 'bg-warning' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $displayText }}
                                    </span>
                                </td>
                                <td>{{ $sekolah->desa_kelurahan ?? '-' }}</td>
                                <td>
                                    @if($sekolah->hasOperator())
                                        <span class="badge bg-success"><i class="fi fi-rr-check me-1"></i>Ada</span>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                        wire:click="edit('{{ $sekolah->sekolah_id }}')">
                                        <i class="fi fi-rr-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('{{ $sekolah->sekolah_id }}', '{{ $sekolah->nama }}')">
                                        <i class="fi fi-rr-trash"></i> Hapus
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="showDetailModal('{{ $sekolah->sekolah_id }}')">
                                        <i class="fi fi-rr-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if($search)
                                        Tidak ada sekolah ditemukan untuk "{{ $search }}"
                                    @else
                                        Belum ada data sekolah. Silakan import data terlebih dahulu.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sekolahList->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $sekolahList->links($this->paginationView()) }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetail && $selectedSekolah)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Sekolah</h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Sekolah ID</label>
                                <p class="mb-0 fw-medium">{{ $selectedSekolah->sekolah_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">NPSN</label>
                                <p class="mb-0 fw-medium"><code>{{ $selectedSekolah->npsn }}</code></p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">Nama Sekolah</label>
                                <p class="mb-0 fw-medium">{{ $selectedSekolah->nama }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status</label>
                                <p class="mb-0">
                                    @php
                                        $status = $selectedSekolah->status_sekolah;
                                        $isNegeri = $status === 'Negeri' || $status == 1;
                                        $isSwasta = $status === 'Swasta' || $status == 2;
                                        $displayText = $isNegeri ? 'Negeri' : ($isSwasta ? 'Swasta' : ($status ?? '-'));
                                        $badgeClass = $isNegeri ? 'bg-success' : ($isSwasta ? 'bg-warning' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $displayText }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Bentuk Pendidikan</label>
                                <p class="mb-0">{{ $selectedSekolah->bentuk_pendidikan_id ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Kode Wilayah</label>
                                <p class="mb-0">{{ $selectedSekolah->kode_wilayah ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Desa/Kelurahan</label>
                                <p class="mb-0">{{ $selectedSekolah->desa_kelurahan ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">Alamat Lengkap</label>
                                <p class="mb-0">{{ $selectedSekolah->alamat_lengkap }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Koordinat</label>
                                <p class="mb-0">
                                    @if($selectedSekolah->lintang && $selectedSekolah->bujur)
                                        {{ $selectedSekolah->lintang }}, {{ $selectedSekolah->bujur }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status Akun Operator</label>
                                <p class="mb-0">
                                    @if($selectedSekolah->hasOperator())
                                        <span class="badge bg-success"><i class="fi fi-rr-check me-1"></i>Sudah Dibuat</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Ada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit Data Sekolah' : 'Tambah Sekolah Baru' }}</h5>
                        <button type="button" class="btn-close" wire:click="cancelCreate"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NPSN <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('form.npsn') is-invalid @enderror"
                                    wire:model="form.npsn" placeholder="Contoh: 12345678">
                                @error('form.npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('form.nama') is-invalid @enderror"
                                    wire:model="form.nama" placeholder="Contoh: SDN 1 CIANJUR">
                                @error('form.nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Sekolah <span class="text-danger">*</span></label>
                                <select class="form-select @error('form.status_sekolah') is-invalid @enderror"
                                    wire:model="form.status_sekolah">
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                                @error('form.status_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desa / Kelurahan</label>
                                <input type="text" class="form-control @error('form.desa_kelurahan') is-invalid @enderror"
                                    wire:model="form.desa_kelurahan" placeholder="Nama Desa">
                                @error('form.desa_kelurahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat Jalan</label>
                                <textarea class="form-control @error('form.alamat_jalan') is-invalid @enderror"
                                    wire:model="form.alamat_jalan" rows="2" placeholder="Alamat lengkap sekolah"></textarea>
                                @error('form.alamat_jalan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check p-3 border rounded bg-light">
                                    <input class="form-check-input" type="checkbox" wire:model="form.generate_account"
                                        id="createGenerateAccount">
                                    <label class="form-check-label" for="createGenerateAccount">
                                        <strong>Generate Akun Operator (OPSD)</strong>
                                        <small class="text-muted d-block">
                                            {{ $isEditMode ? 'Jika dicentang, akun akan dibuat/diupdate sesuai NPSN.' : 'Username & Password akan diset sesuai NPSN.' }}
                                        </small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelCreate">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="{{ $isEditMode ? 'update' : 'store' }}">
                            <i class="fi fi-rr-disk me-1"></i> {{ $isEditMode ? 'Update Data' : 'Simpan Data' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Modal Removed -->

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmReset() {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Semua data sekolah akan dihapus! User OPSD akan di-unlink.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('resetData');
                    }
                });
            }


            function confirmDelete(id, nama) {
                Swal.fire({
                    title: 'Hapus Sekolah?',
                    text: "Anda akan menghapus data sekolah " + nama,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('delete', id);
                    }
                });
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('import-success', (event) => {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
                    if (modal) modal.hide();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: event.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });

                Livewire.on('import-error', (event) => {
                    Swal.fire({
                        title: 'Error!',
                        text: event.message,
                        icon: 'error'
                    });
                });
            });
        </script>
    @endpush