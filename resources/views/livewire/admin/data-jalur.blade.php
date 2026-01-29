<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Jalur Pendaftaran</h4>
            <p class="text-muted mb-0">Kelola jalur pendaftaran dan persyaratan berkas.</p>
        </div>
        <div>
            <button wire:click="create" class="btn btn-primary">
                <i class="fi fi-rr-add me-2"></i> Tambah Jalur
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
                        <input type="text" class="form-control border-start-0" placeholder="Cari Jalur..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $jalurList->total() }} Jalur</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Nama Jalur</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Berkas</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jalurList as $item)
                            <tr wire:key="jalur-{{ $item->id }}">
                                <td>
                                    <span class="fw-medium d-block">{{ $item->nama }}</span>
                                    <small class="text-muted">{{ Str::limit($item->deskripsi, 30) }}</small>
                                </td>
                                <td>
                                    @if($item->start_date && $item->end_date)
                                        <small class="d-block text-nowrap">Mulai:
                                            {{ $item->start_date->format('d M Y') }}</small>
                                        <small class="d-block text-nowrap">Selesai:
                                            {{ $item->end_date->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $item->berkas_count }} Berkas</span>
                                </td>
                                <td class="text-center">
                                    @if($item->aktif)
                                        <span class="badge bg-success-subtle text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button wire:confirm="Yakin ingin menghapus jalur ini?"
                                        wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-outline-danger">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada data jalur pendaftaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jalurList->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $jalurList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($showFormModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit Jalur' : 'Tambah Jalur' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Jalur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    wire:model="nama" placeholder="Contoh: Jalur Zonasi">
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                    wire:model="deskripsi" rows="2" placeholder="Keterangan jalur..."></textarea>
                                @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        wire:model="start_date">
                                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        wire:model="end_date">
                                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isAktif" wire:model="aktif">
                                    <label class="form-check-label" for="isAktif">Jalur Aktif</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Berkas Persyaratan</label>
                                <div class="card bg-light border-0 p-3">
                                    <div class="row">
                                        @forelse($allBerkas as $berkas)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="{{ $berkas->id }}"
                                                        id="berkas-{{ $berkas->id }}" wire:model="selectedBerkas">
                                                    <label class="form-check-label" for="berkas-{{ $berkas->id }}">
                                                        {{ $berkas->nama }}
                                                        @if($berkas->is_required)
                                                            <span class="badge bg-danger-subtle text-danger rounded-pill ms-1"
                                                                style="font-size: 10px;">Wajib</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-muted small">Belum ada master berkas. Silakan tambahkan di
                                                menu Data Berkas.</div>
                                        @endforelse
                                    </div>
                                </div>
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