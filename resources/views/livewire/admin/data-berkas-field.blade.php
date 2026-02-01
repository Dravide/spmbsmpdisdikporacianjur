<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Konfigurasi Form Input</h4>
            <p class="text-muted mb-0">Atur field tambahan untuk berkas: <strong>{{ $berkas->nama }}</strong></p>
        </div>
        <a href="{{ route('admin.berkas') }}" class="btn btn-secondary"><i
                class="fi fi-rr-arrow-left me-2"></i>Kembali</a>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Field Input</h6>
                <button wire:click="addField" class="btn btn-primary btn-sm"><i class="fi fi-rr-plus me-2"></i>Tambah
                    Field</button>
            </div>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 25%;">Label Input</th>
                            <th style="width: 20%;">Nama Variabel (Unik)</th>
                            <th style="width: 20%;">Grup / Kelompok</th>
                            <th style="width: 15%;">Tipe Data</th>
                            <th style="width: 10%;" class="text-center">Wajib?</th>
                            <th style="width: 10%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formFields as $index => $field)
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model="formFields.{{ $index }}.label" placeholder="Contoh: Nilai Matematika">
                                    @error("formFields.{$index}.label") <span
                                    class="text-danger small">{{ $message }}</span> @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model="formFields.{{ $index }}.name" placeholder="Contoh: mtk_sem1">
                                    @error("formFields.{$index}.name") <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model="formFields.{{ $index }}.group" placeholder="Contoh: Semester 1"
                                        list="groupList">
                                </td>
                                <td>
                                    <select class="form-select form-select-sm" wire:model="formFields.{{ $index }}.type">
                                        <option value="number">Angka (0-100)</option>
                                        <option value="text">Teks Singkat</option>
                                        <option value="textarea">Teks Panjang</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="formFields.{{ $index }}.required">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button wire:click="duplicateField({{ $index }})"
                                        class="btn btn-outline-info btn-sm me-1" title="Duplikat">
                                        <i class="fi fi-rr-copy"></i>
                                    </button>
                                    <button wire:click="removeField({{ $index }})" class="btn btn-outline-danger btn-sm"
                                        title="Hapus">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-settings-sliders fs-1 d-block mb-2"></i>
                                    Belum ada field input.<br>
                                    Klik tombol <span class="fw-bold">Tambah Field</span> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <datalist id="groupList">
                <option value="Semester 1">
                <option value="Semester 2">
                <option value="Semester 3">
                <option value="Semester 4">
                <option value="Semester 5">
                <option value="Data Sekolah">
                <option value="Data Pribadi">
            </datalist>

        </div>
        <div class="card-footer bg-white text-end py-3">
            <button wire:click="save" class="btn btn-success">
                <i class="fi fi-rr-disk me-2"></i>Simpan Konfigurasi
            </button>
        </div>
    </div>
</div>