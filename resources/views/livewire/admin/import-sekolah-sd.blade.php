<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Import Data Sekolah Dasar</h4>
            <p class="text-muted mb-0">Upload data sekolah masal menggunakan file CSV.</p>
        </div>
        <div>
            <a href="{{ route('admin.sekolah') }}" class="btn btn-secondary" wire:navigate>
                <i class="fi fi-rr-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @if(!$showPreview)
                <!-- Upload Form -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="icon-circle bg-primary-subtle text-primary mx-auto mb-3"
                            style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fi fi-rr-file-upload fs-1"></i>
                        </div>
                        <h5>Upload File CSV</h5>
                        <p class="text-muted">Format:
                            sekolah_id|npsn|nama|status_sekolah|...<br>
                            Delimiter: Pipe (|), Semicolon (;), or Comma (,)
                        </p>
                    </div>

                    <div class="col-md-6 mx-auto">
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-lg" wire:model="file" accept=".csv,.txt">
                        </div>

                        <div wire:loading wire:target="file" class="text-primary">
                            <span class="spinner-border spinner-border-sm me-1"></span> Memproses file...
                        </div>

                        @error('file')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @else
                <!-- Preview Data -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Preview Data ({{ count($previewData) }} baris pertama)</h5>
                        <p class="text-muted mb-0">Periksa sampel data sebelum melakukan import.</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="generateAccounts"
                                id="generateAccounts">
                            <label class="form-check-label" for="generateAccounts">
                                <strong>Generate Akun OPSD</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive border rounded mb-4" style="max-height: 500px;">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>#</th>
                                <th>NPSN</th>
                                <th>Nama Sekolah</th>
                                <th>Status</th>
                                <th>Desa/Kelurahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $row['npsn'] ?? '-' }}</code></td>
                                    <td>{{ $row['nama'] ?? '-' }}</td>
                                    <td>{{ $row['status_sekolah'] ?? '-' }}</td>
                                    <td>{{ $row['desa_kelurahan'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="" x-data="{
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
                    <div x-show="importing" class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-sm font-medium text-primary">Importing...</span>
                            <span class="text-sm font-medium text-primary" x-text="Math.round(progress) + '%'"></span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-primary transition-all duration-300 progress-bar-striped progress-bar-animated"
                                role="progressbar" :style="'width: ' + progress + '%'" :aria-valuenow="progress"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-center mt-2 text-muted small">
                            Memproses <span x-text="processed"></span> dari <span x-text="total"></span> data...
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2" x-show="!importing">
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelImport">
                            <i class="fi fi-rr-cross me-1"></i> Batal / Upload Ulang
                        </button>
                        <button type="button" class="btn btn-primary" @click="startImport">
                            <i class="fi fi-rr-check me-1"></i> Mulai Import Data
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endassets

@script
<script>
    Livewire.on('import-success', (event) => {
        Swal.fire({
            title: 'Berhasil!',
            text: event.message,
            icon: 'success',
            timer: 3000,
            showConfirmButton: true
        }).then(() => {
            // Optional: redirect logic if needed
        });
    });

    Livewire.on('import-error', (event) => {
        Swal.fire({
            title: 'Error!',
            text: event.message,
            icon: 'error'
        });
    });
</script>
@endscript