<div>
    @if($isOpen)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Siswa</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="update">
                            <!-- Identitas -->
                            <h6 class="text-primary border-bottom pb-2 mb-3">Identitas Peserta Didik</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label small">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="formData.nama" required>
                                    @error('formData.nama') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">NISN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="formData.nisn" required>
                                    @error('formData.nisn') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">NIK</label>
                                    <input type="text" class="form-control" wire:model="formData.nik">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">No. KK</label>
                                    <input type="text" class="form-control" wire:model="formData.no_kk">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Tempat Lahir</label>
                                    <input type="text" class="form-control" wire:model="formData.tempat_lahir">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Tanggal Lahir</label>
                                    <input type="date" class="form-control" wire:model="formData.tanggal_lahir">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Jenis Kelamin</label>
                                    <select class="form-select" wire:model="formData.jenis_kelamin">
                                        <option value="">Pilih</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Kebutuhan Khusus</label>
                                    <input type="text" class="form-control" wire:model="formData.kebutuhan_khusus">
                                </div>
                            </div>

                            <!-- Data Orang Tua -->
                            <h6 class="text-primary border-bottom pb-2 mb-3">Data Orang Tua</h6>
                            <div class="row g-3 mb-4">
                                <!-- Ibu -->
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body p-3">
                                            <strong class="d-block mb-2 text-muted">Data Ibu Kandung</strong>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Nama Ibu</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.nama_ibu_kandung">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Pekerjaan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.pekerjaan_ibu">
                                            </div>
                                            <div>
                                                <label class="form-label small mb-1">Penghasilan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.penghasilan_ibu">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Ayah -->
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body p-3">
                                            <strong class="d-block mb-2 text-muted">Data Ayah</strong>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Nama Ayah</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.nama_ayah">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Pekerjaan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.pekerjaan_ayah">
                                            </div>
                                            <div>
                                                <label class="form-label small mb-1">Penghasilan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.penghasilan_ayah">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Wali -->
                                <div class="col-md-4">
                                    <div class="card bg-light border-0 h-100">
                                        <div class="card-body p-3">
                                            <strong class="d-block mb-2 text-muted">Data Wali (Opsional)</strong>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Nama Wali</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.nama_wali">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small mb-1">Pekerjaan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.pekerjaan_wali">
                                            </div>
                                            <div>
                                                <label class="form-label small mb-1">Penghasilan</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    wire:model="formData.penghasilan_wali">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat & Lainnya -->
                            <h6 class="text-primary border-bottom pb-2 mb-3">Alamat & Data Lainnya</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small">Alamat Jalan</label>
                                    <textarea class="form-control" wire:model="formData.alamat_jalan" rows="2"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Kecamatan</label>
                                    <select class="form-select" wire:model.live="selectedKecamatanCode">
                                        <option value="">Pilih</option>
                                        @foreach($kecamatanList as $kec)
                                            <option value="{{ $kec['code'] }}">{{ $kec['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Desa / Kelurahan</label>
                                    <select class="form-select" wire:model.live="selectedDesaCode">
                                        <option value="">Pilih</option>
                                        @foreach($desaList as $desa)
                                            <option value="{{ $desa['code'] }}">{{ $desa['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Dusun</label>
                                    <input type="text" class="form-control" wire:model="formData.nama_dusun">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">RT</label>
                                    <input type="text" class="form-control" wire:model="formData.rt">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">RW</label>
                                    <input type="text" class="form-control" wire:model="formData.rw">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Lintang (Latitude)</label>
                                    <input type="text" class="form-control" wire:model="formData.lintang">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Bujur (Longitude)</label>
                                    <input type="text" class="form-control" wire:model="formData.bujur">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">No. KIP</label>
                                    <input type="text" class="form-control" wire:model="formData.no_KIP">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">No. PKH</label>
                                    <input type="text" class="form-control" wire:model="formData.no_pkh">
                                </div>
                            </div>

                            <!-- Submit Button Hidden, triggered by footer button -->
                            <button type="submit" class="d-none"></button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled">
                            <span wire:loading wire:target="update" class="spinner-border spinner-border-sm me-1"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>