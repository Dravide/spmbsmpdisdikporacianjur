<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Berkas Persyaratan</h4>
            <p class="text-muted mb-0">Kelola master data berkas yang dibutuhkan untuk pendaftaran.</p>
        </div>
        <div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fi fi-rr-add me-2"></i> Tambah Berkas
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari Berkas..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $berkasList->total() }} Berkas</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Nama Berkas</th>
                            <th class="border-0">Jenis</th>
                            <th class="border-0">Deskripsi</th>
                            <th class="border-0 text-center">Maks. Ukuran</th>
                            <th class="border-0 text-center">Wajib?</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkasList as $item)
                            <tr wire:key="berkas-{{ $item->id }}">
                                <td>
                                    <span class="fw-medium">{{ $item->nama }}</span>
                                </td>
                                <td>
                                    @if($item->jenis == 'Berkas Umum')
                                        <span class="badge bg-primary-subtle text-primary">{{ $item->jenis }}</span>
                                    @elseif($item->jenis == 'Berkas Khusus')
                                        <span class="badge bg-warning-subtle text-warning">{{ $item->jenis }}</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info">{{ $item->jenis }}</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->deskripsi, 50) ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">
                                        {{ number_format($item->max_size_kb / 1024, 1) }} MB
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->is_required)
                                        <span class="badge bg-success-subtle text-success">Ya</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Opsional</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.berkas.fields', $item->id) }}"
                                        class="btn btn-sm btn-outline-info me-1" title="Atur Form Input">
                                        <i class="fi fi-rr-settings-sliders"></i>
                                    </a>
                                    <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus berkas ini?"
                                        wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-outline-danger">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada data berkas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($berkasList->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $berkasList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($showFormModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit Berkas' : 'Tambah Berkas' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Berkas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    wire:model="nama" placeholder="Contoh: Kartu Keluarga">
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Berkas <span class="text-danger">*</span></label>
                                <select class="form-select @error('jenis') is-invalid @enderror" wire:model="jenis">
                                    <option value="Berkas Umum">Berkas Umum</option>
                                    <option value="Berkas Khusus">Berkas Khusus</option>
                                    <option value="Berkas Tambahan">Berkas Tambahan</option>
                                </select>
                                @error('jenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                    wire:model="deskripsi" rows="3" placeholder="Keterangan tambahan..."></textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Maksimal Ukuran File <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('max_size_kb') is-invalid @enderror"
                                        wire:model="max_size_kb" min="100" max="10240">
                                    <span class="input-group-text">KB</span>
                                </div>
                                <small class="text-muted">Rentang: 100 KB - 10240 KB (10 MB). Default: 2048 KB (2
                                    MB)</small>
                                @error('max_size_kb') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isRequired"
                                        wire:model="is_required">
                                    <label class="form-check-label" for="isRequired">Wajib diupload?</label>
                                </div>
                                <small class="text-muted">Jika aktif, siswa wajib mengupload berkas ini saat
                                    pendaftaran.</small>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light"
                                wire:click="$set('showFormModal', false)">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>