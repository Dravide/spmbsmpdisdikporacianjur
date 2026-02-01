<div>
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-4">Pengaturan Aplikasi</h4>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form wire:submit="save">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Aplikasi (Title)</label>
                                <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                    wire:model="app_name" placeholder="Contoh: SPMB Cianjur">
                                @error('app_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text text-muted">Akan muncul di Tab Browser dan Title Bar.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Logo Text (Header)</label>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                            style="height: 60px; min-width: 100px;">
                                            @if ($app_logo_text_image)
                                                <img src="{{ $app_logo_text_image->temporaryUrl() }}" class="img-fluid"
                                                    style="max-height: 40px;">
                                            @elseif($existing_logo_text_image)
                                                <img src="{{ asset('storage/' . $existing_logo_text_image) }}"
                                                    class="img-fluid" style="max-height: 40px;">
                                            @else
                                                <span class="text-muted small">No Image</span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file"
                                                class="form-control @error('app_logo_text_image') is-invalid @enderror"
                                                wire:model="app_logo_text_image" accept="image/*">
                                            @error('app_logo_text_image') <div class="invalid-feedback">{{ $message }}
                                            </div> @enderror
                                            <div class="form-text text-muted">Upload gambar untuk mengganti teks.
                                                (Opsional)</div>
                                        </div>
                                    </div>
                                </div>

                                <input type="text" class="form-control @error('app_logo_text') is-invalid @enderror"
                                    wire:model="app_logo_text" placeholder="Contoh: SPMB">
                                @error('app_logo_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text text-muted">Teks alternatif jika gambar tidak ada.</div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mode Situs</label>
                            <div class="d-flex gap-4 p-3 border rounded bg-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="site_mode" id="modeNormal"
                                        value="normal">
                                    <label class="form-check-label" for="modeNormal">
                                        <i class="fi fi-rr-check-circle text-success me-1"></i> Normal
                                        <small class="d-block text-muted">Web dapat diakses publik.</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="site_mode"
                                        id="modeMaintenance" value="maintenance">
                                    <label class="form-check-label" for="modeMaintenance">
                                        <i class="fi fi-rr-tools text-warning me-1"></i> Maintenance
                                        <small class="d-block text-muted">Menampilkan halaman perbaikan.</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="site_mode"
                                        id="modeComingSoon" value="coming_soon">
                                    <label class="form-check-label" for="modeComingSoon">
                                        <i class="fi fi-rr-rocket-lunch text-info me-1"></i> Coming Soon
                                        <small class="d-block text-muted">Menampilkan halaman segera hadir.</small>
                                    </label>
                                </div>
                            </div>
                            @error('site_mode') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Logo Gambar</label>

                            <div class="d-flex align-items-center gap-3">
                                <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    @if ($app_logo_image)
                                        <img src="{{ $app_logo_image->temporaryUrl() }}" class="img-fluid"
                                            style="max-height: 60px;">
                                    @elseif($existing_logo_image)
                                        <img src="{{ asset('storage/' . $existing_logo_image) }}" class="img-fluid"
                                            style="max-height: 60px;">
                                    @else
                                        <i class="fi fi-rr-picture text-muted fs-3"></i>
                                    @endif
                                </div>
                                <div>
                                    <input type="file"
                                        class="form-control @error('app_logo_image') is-invalid @enderror"
                                        wire:model="app_logo_image" accept="image/*">
                                    @error('app_logo_image') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">Format: PNG, JPG. Maks 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fi fi-rr-disk me-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>