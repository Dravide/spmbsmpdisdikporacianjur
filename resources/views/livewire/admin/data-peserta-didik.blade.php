<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Peserta Didik</h4>
            <p class="text-muted mb-0">Kelola master data seluruh peserta didik.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger" onclick="confirmReset()">
                <i class="fi fi-rr-trash me-1"></i> Reset Data
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fi fi-rr-file-import me-1"></i> Import Data
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
                        <input type="text" class="form-control border-start-0"
                            placeholder="Cari Nama, NISN, atau NIK..." wire:model.live.debounce.300ms="search">
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#filterOffcanvas">
                            <i class="fi fi-rr-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Total: {{ $pesertaDidikList->total() }} Siswa</span>
                </div>
            </div>
        </div>

        <!-- Filter Offcanvas -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" wire:ignore.self>
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Filter Lanjutan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div class="mb-3" wire:ignore>
                    <label class="form-label">Sekolah</label>
                    <select class="form-select" id="filter-sekolah">
                        <option value="">Semua Sekolah</option>
                        @foreach ($sekolahList as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3" wire:ignore>
                    <label class="form-label">Kecamatan</label>
                    <select class="form-select" id="filter-kecamatan">
                        <option value="">Semua Kecamatan</option>
                        @foreach ($kecamatanList as $kec)
                            <option value="{{ $kec['name'] }}">{{ $kec['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Desa/Kelurahan</label>
                    <!-- Note: This one needs to update when kecamatan changes, so we can't fully ignore it or we need to re-init. 
                          Strategy: Wrap in a key that updates, or use JS to update options. 
                          Simpler for Livewire: Standard select or re-init select2 on update. 
                          Let's try standard select for Desa first as it changes dynamically, 
                          OR re-init select2 in hook. Given user asked for Select2, let's try to make it work. -->
                    <div wire:key="desa-select-{{ $filterKecamatan }}">
                        <select class="form-select" id="filter-desa" wire:model.live="filterDesa">
                            <option value="">Semua Desa</option>
                            @foreach ($desaList as $desa)
                                <option value="{{ $desa['name'] }}">{{ $desa['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" wire:model.live="filterJk">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger" wire:click="resetFilters">Reset Filter</button>
                    <button class="btn btn-primary" data-bs-dismiss="offcanvas">Terapkan</button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Nama Lengkap</th>
                            <th class="border-0">L/P</th>
                            <th class="border-0">NISN / NIK</th>
                            <th class="border-0">TTL</th>
                            <th class="border-0">Default Password</th>
                            <th class="border-0">Asal Sekolah</th>
                            <th class="border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesertaDidikList as $item)
                            <tr>
                                <td>
                                    <span class="fw-medium d-block">{{ $item->nama }}</span>
                                    <small class="text-muted">{{ $item->peserta_didik_id }}</small>
                                </td>
                                <td>{{ $item->jenis_kelamin }}</td>
                                <td>
                                    <div class="small">NISN: {{ $item->nisn ?? '-' }}</div>
                                    <div class="small text-muted">NIK: {{ $item->nik ?? '-' }}</div>
                                </td>
                                <td>
                                    {{ $item->tempat_lahir }},
                                    {{ $item->tanggal_lahir ? $item->tanggal_lahir->locale('id')->translatedFormat('d F Y') : '-' }}
                                </td>
                                <td>
                                    <div x-data="{ show: false }" class="d-flex align-items-center">
                                        <span x-show="!show" class="text-muted small">********</span>
                                        <span x-show="show" class="fw-bold text-dark small">
                                            {{ $item->tanggal_lahir ? $item->tanggal_lahir->format('Ymd') : '-' }}
                                        </span>
                                        <button @click="show = !show"
                                            class="btn btn-sm btn-link p-0 ms-2 text-decoration-none text-secondary"
                                            title="Lihat Password Default">
                                            <i class="fi" :class="show ? 'fi-rr-eye-crossed' : 'fi-rr-eye'"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    @if($item->sekolah)
                                        {{ $item->sekolah->nama }}
                                    @else
                                        <span class="text-muted small">ID: {{ $item->sekolah_id }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-warning" wire:click="edit({{ $item->id }})">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->nama) }}')">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data peserta didik.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pesertaDidikList->hasPages())
            <div class="card-footer bg-transparent border-0">
                {{ $pesertaDidikList->links($this->paginationView()) }}
            </div>
        @endif
    </div>

    <!-- Edit Modal -->
    @if($isEditMode)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Siswa</h5>
                        <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" wire:model="form.nama">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <input type="text" class="form-control" wire:model="form.jenis_kelamin">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NISN</label>
                                <input type="text" class="form-control" wire:model="form.nisn">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIK</label>
                                <input type="text" class="form-control" wire:model="form.nik">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" wire:model="form.tempat_lahir">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" wire:model="form.tanggal_lahir">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Ibu Kandung</label>
                                <input type="text" class="form-control" wire:model="form.nama_ibu_kandung">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="update">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Peserta Didik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(!$showPreview)
                        <!-- Upload Form -->
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fi fi-rr-file-upload fs-1 text-primary"></i>
                            </div>
                            <h6>Upload File CSV/Text</h6>
                            <p class="text-muted small">Format: Pipe Delimited (|) - Format Dapodik</p>

                            <div class="mb-3">
                                <input type="file" class="form-control" wire:model="file" accept=".csv,.txt,.xlsx,.xls">
                            </div>

                            <div wire:loading wire:target="file" class="text-primary">
                                <span class="spinner-border spinner-border-sm me-1"></span> Memproses file...
                            </div>
                            @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    @else
                        <!-- Preview Data -->
                        <div class="mb-3">
                            <h6>Preview Data ({{ count($previewData) }} baris)</h6>
                        </div>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="bg-light sticky-top">
                                    <tr>
                                        <th>Nama</th>
                                        <th>ID Dapodik</th>
                                        <th>NISN</th>
                                        <th>Sekolah ID</th>
                                        <th>TTL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($previewData, 0, 50) as $row)
                                        <tr>
                                            <td>{{ $row['nama'] ?? '-' }}</td>
                                            <td><small>{{ $row['peserta_didik_id'] ?? '-' }}</small></td>
                                            <td>{{ $row['nisn'] ?? '-' }}</td>
                                            <td>{{ $row['sekolah_id'] ?? '-' }}</td>
                                            <td>{{ $row['tempat_lahir'] }}, {{ $row['tanggal_lahir'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(count($previewData) > 50)
                                <div class="text-center py-2 text-muted small">... dan {{ count($previewData) - 50 }} data
                                    lainnya</div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($showPreview)
                        <div class="w-100" x-data="{
                                                progress: 0,
                                                importing: @entangle('isImporting'),
                                                total: @entangle('totalToImport'),
                                                processed: @entangle('importedCount'),
                                                async startImport() {
                                                    this.progress = 0;
                                                    await @this.call('startImport');
                                                    this.processNextBatch();
                                                },
                                                async processNextBatch() {
                                                    if (!this.importing) return;
                                                    let result = await @this.call('processBatch');
                                                    this.progress = result;
                                                    if (this.processed < this.total && this.importing) {
                                                        setTimeout(() => this.processNextBatch(), 50);
                                                    }
                                                }
                                            }">
                            <div x-show="importing" class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Importing...</span>
                                    <span x-text="Math.round(progress) + '%'"></span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" :style="'width: ' + progress + '%'"></div>
                                </div>
                                <div class="text-center mt-1"><span x-text="processed"></span> / <span
                                        x-text="total"></span></div>
                            </div>
                            <div class="text-end" x-show="!importing">
                                <button class="btn btn-secondary" wire:click="cancelImport">Batal</button>
                                <button class="btn btn-primary" @click="startImport">Import Sekarang</button>
                            </div>
                        </div>
                    @else
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Init Sekolah Select2
            $('#filter-sekolah').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#filterOffcanvas'),
                placeholder: 'Pilih Sekolah',
                allowClear: true
            }).on('change', function (e) {
                @this.set('filterSekolah', $(this).val());
            });

            // Init Kecamatan Select2
            $('#filter-kecamatan').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#filterOffcanvas'),
                placeholder: 'Pilih Kecamatan',
                allowClear: true
            }).on('change', function (e) {
                @this.set('filterKecamatan', $(this).val());
            });

            // Init Desa Select2
            function initDesaSelect2() {
                $('#filter-desa').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#filterOffcanvas'),
                    placeholder: 'Pilih Desa/Kelurahan',
                    allowClear: true
                }).on('change', function (e) {
                    @this.set('filterDesa', $(this).val());
                });
            }

            initDesaSelect2();

            // Re-init Desa when Kecamatan updates
            Livewire.on('kecamatan-updated', () => {
                // Wait for Livewire to finish DOM update
                setTimeout(() => {
                    initDesaSelect2();
                }, 100);
            });

            // Listen for reset
            Livewire.on('filters-reset', () => {
                $('#filter-sekolah').val(null).trigger('change');
                $('#filter-kecamatan').val(null).trigger('change');
                $('#filter-desa').val(null).trigger('change');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmReset() {
            Swal.fire({
                title: 'Hapus Semua Data?',
                text: "Seluruh data peserta didik akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset Data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resetData');
                }
            });
        }

        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Siswa?',
                text: "Hapus data: " + nama,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('import-success', (event) => {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('importModal')).hide();
                Swal.fire('Berhasil', event.message, 'success');
            });
            Livewire.on('import-error', (event) => {
                Swal.fire('Error', event.message, 'error');
            });
        });
    </script>
@endpush