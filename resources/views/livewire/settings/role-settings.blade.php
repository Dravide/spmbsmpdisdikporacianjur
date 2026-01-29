<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pengaturan Role</h4>
            <p class="text-muted mb-0">Kelola batas login dan timeout sesi untuk setiap role.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($settings as $index => $setting)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $setting['role_label'] }}</h6>
                        <span class="badge bg-secondary">{{ $setting['role'] }}</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Maksimal Lokasi Login</label>
                            <input type="number"
                                class="form-control @error("settings.{$index}.max_login_locations") is-invalid @enderror"
                                wire:model.defer="settings.{{ $index }}.max_login_locations" min="1" max="100">
                            @error("settings.{$index}.max_login_locations")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jumlah lokasi (IP) berbeda yang diperbolehkan login bersamaan.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Timeout Sesi (menit)</label>
                            <input type="number"
                                class="form-control @error("settings.{$index}.session_timeout_minutes") is-invalid @enderror"
                                wire:model.defer="settings.{{ $index }}.session_timeout_minutes" min="5" max="1440">
                            @error("settings.{$index}.session_timeout_minutes")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Sesi akan berakhir setelah tidak aktif selama waktu ini.</small>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="multiSession{{ $index }}"
                                wire:model.defer="settings.{{ $index }}.allow_multiple_sessions">
                            <label class="form-check-label" for="multiSession{{ $index }}">
                                Izinkan Multiple Sessions
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary w-100" wire:click="updateSetting({{ $index }})"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateSetting({{ $index }})">Simpan</span>
                            <span wire:loading wire:target="updateSetting({{ $index }})">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>