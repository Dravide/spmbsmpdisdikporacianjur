<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pemetaan Domisili</h4>
            <p class="text-muted mb-0">Kelola zona domisili untuk jalur pendaftaran domisili setiap sekolah.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="fi fi-rr-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Cari sekolah..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $sekolahs->total() }} Sekolah</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NPSN</th>
                            <th>Nama Sekolah</th>
                            <th>Alamat</th>
                            <th class="text-center">Zona Terdaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sekolahs as $sekolah)
                            <tr>
                                <td><code>{{ $sekolah->npsn }}</code></td>
                                <td class="fw-medium">{{ $sekolah->nama }}</td>
                                <td class="text-muted small">{{ Str::limit($sekolah->alamat_jalan, 40) }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $sekolah->zona_domisili_count > 0 ? 'success' : 'secondary' }}-subtle text-{{ $sekolah->zona_domisili_count > 0 ? 'success' : 'secondary' }}">
                                        {{ $sekolah->zona_domisili_count }} Zona
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary"
                                        wire:click="openDetail('{{ $sekolah->sekolah_id }}')">
                                        <i class="fi fi-rr-list me-1"></i> Detail
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

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedSekolah)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">Zona Domisili</h5>
                            <p class="text-muted mb-0 small">{{ $selectedSekolah->nama }}</p>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetail"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <p class="text-muted mb-0 small">
                                Siswa yang berdomisili di zona berikut dapat mendaftar melalui jalur domisili.
                            </p>
                            <button class="btn btn-success btn-sm me-2" wire:click="startImport">
                                <i class="fi fi-rr-file-import me-1"></i> Import Excel
                            </button>
                            <button class="btn btn-primary btn-sm" wire:click="createZona">
                                <i class="fi fi-rr-add me-1"></i> Tambah Zona
                            </button>
                        </div>

                        @if(count($zonaList) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kecamatan</th>
                                            <th>Desa/Kelurahan</th>
                                            <th>RW</th>
                                            <th>RT</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zonaList as $zona)
                                            <tr>
                                                <td class="fw-medium">{{ $zona['kecamatan'] }}</td>
                                                <td>{{ $zona['desa'] ?: '-' }}</td>
                                                <td>{{ $zona['rw'] ?: '-' }}</td>
                                                <td>{{ $zona['rt'] ?: '-' }}</td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        wire:click="editZona({{ $zona['id'] }})">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        wire:confirm="Yakin ingin menghapus zona ini?"
                                                        wire:click="deleteZona({{ $zona['id'] }})">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fi fi-rr-map-marker d-block mb-2 fs-1"></i>
                                Belum ada zona domisili untuk sekolah ini.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeDetail">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Modal -->
    @if($showFormModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.7);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $zonaId ? 'Edit' : 'Tambah' }} Zona Domisili</h5>
                        <button type="button" class="btn-close" wire:click="$set('showFormModal', false)"></button>
                    </div>
                    <form wire:submit.prevent="saveZona">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <select class="form-select @error('kecamatan') is-invalid @enderror"
                                    wire:model.live="kecamatan">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach($kecamatanList as $kec)
                                        <option value="{{ $kec['name'] }}">{{ $kec['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                                <select class="form-select @error('desa') is-invalid @enderror"
                                    wire:model="desa" {{ empty($desaList) ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Desa --</option>
                                    @foreach($desaList as $ds)
                                        <option value="{{ $ds['name'] }}">{{ $ds['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('desa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if(empty($kecamatan))
                                    <small class="text-muted">Pilih kecamatan terlebih dahulu</small>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RW <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rw') is-invalid @enderror"
                                        wire:model="rw" placeholder="Contoh: 01">
                                    @error('rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">RT <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('rt') is-invalid @enderror"
                                        wire:model="rt" placeholder="Contoh: 01">
                                    @error('rt') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    <!-- Import Modal -->
    @if($showImportModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.7); overflow-y: auto;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Zona Domisili (Excel)</h5>
                        <button type="button" class="btn-close" wire:click="closeImport"></button>
                    </div>
                    <div class="modal-body">
                        <!-- File Upload -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">1. Upload File Excel</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" wire:click="downloadTemplate">
                                    <i class="fi fi-rr-download me-1"></i> Download Template
                                </button>
                            </div>
                            <p class="text-muted small mb-2">
                                Format wajib (Header): <code>Kecamatan | Desa | RW | RT</code>. <br>
                                Gunakan format <strong>.xlsx</strong> atau <strong>.xls</strong>.
                            </p>
                            <input type="file" class="form-control" wire:model.live="importFile" accept=".xlsx, .xls">
                            @error('importFile') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="importFile" class="text-primary mt-2 small">
                                <i class="fi fi-rr-spinner fi-spin me-1"></i> Memproses file...
                            </div>
                        </div>

                        <!-- Preview Table -->
                        @if(!empty($previewData))
                            <div class="mb-3">
                                <label class="form-label fw-bold">2. Preview Validasi</label>
                                <div class="table-responsive border rounded" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-striped mb-0 small">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Kecamatan</th>
                                                <th>Desa</th>
                                                <th>RW</th>
                                                <th>RT</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($previewData as $row)
                                                <tr class="{{ $row['status'] === 'Invalid' ? 'table-danger' : '' }}">
                                                    <td>{{ $row['kecamatan'] }}</td>
                                                    <td>{{ $row['desa'] }}</td>
                                                    <td>{{ $row['rw'] }}</td>
                                                    <td>{{ $row['rt'] }}</td>
                                                    <td>
                                                        @if($row['status'] === 'Valid')
                                                            <span class="badge bg-success">Valid</span>
                                                        @else
                                                            <span class="badge bg-danger" title="{{ $row['error'] }}">
                                                                Invalid: {{ $row['error'] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Menampilkan 100 baris pertama.</small>
                                    @if(!$canImport)
                                        <div class="text-danger small fw-bold">
                                            <i class="fi fi-rr-exclamation me-1"></i> Data tidak valid ditemukan. Tidak dapat mengimport.
                                        </div>
                                    @else
                                        <div class="text-success small fw-bold">
                                            <i class="fi fi-rr-check me-1"></i> Semua data valid. Siap diimport.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeImport">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="saveImport" 
                            {{ !$canImport ? 'disabled' : '' }}>
                            <i class="fi fi-rr-file-import me-1"></i> Import Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>