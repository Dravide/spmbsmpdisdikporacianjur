<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pemetaan Domisili</h4>
            <p class="text-muted mb-0">Kelola zona domisili untuk jalur pendaftaran domisili sekolah Anda.</p>
        </div>
        <button class="btn btn-primary" wire:click="createZona">
            <i class="fi fi-rr-add me-1"></i> Tambah Zona
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if(count($zonaList) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
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
                <div class="text-center py-5 text-muted">
                    <div class="mb-3">
                        <i class="fi fi-rr-map-marker fs-1 bg-light p-3 rounded-circle"></i>
                    </div>
                    <h5>Belum ada zona domisili</h5>
                    <p class="mb-3">Tambahkan wilayah yang masuk dalam zonasi sekolah Anda.</p>
                    <button class="btn btn-primary btn-sm" wire:click="createZona">
                        <i class="fi fi-rr-plus me-1"></i> Tambah Zona Pertama
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Form Modal -->
    @if($showFormModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
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
</div>
