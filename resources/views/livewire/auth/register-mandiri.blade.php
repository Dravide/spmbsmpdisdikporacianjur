<div class="row g-0 justify-content-center align-items-center bg-body-tertiary h-100vh">
    <div class="col-12 col-md-10 col-lg-8 col-xl-7">
        <div class="card border-0 rounded-0 overflow-hidden shadow-sm">
            <div class="card-body d-flex align-items-center p-0">
                <div class="row w-100 g-0">
                    <div class="col-12">
                        <div class="p-4 p-lg-5">
                            <div class="mb-5 text-center">
                                <a href="{{ route('login') }}" class="d-inline-block mb-4">
                                    @if(function_exists('get_setting') && get_setting('app_logo_image'))
                                        <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo" class="h-40px">
                                    @else
                                        <img src="{{ asset('templates/assets/images/logo.svg') }}" alt="Logo" class="h-40px">
                                    @endif
                                </a>
                                <h1 class="h3 fw-bold mb-1">Pendaftaran Akun Mandiri</h1>
                                <p class="text-muted mb-0">Khusus untuk siswa dari luar wilayah atau sekolah tidak terdaftar.</p>
                            </div>

                            <!-- Progress Indicator -->
                            <div class="position-relative mb-5">
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <i class="fi fi-rr-cross-circle me-2"></i> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                        <i class="fi fi-rr-check-circle me-2"></i> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                        style="width: {{ ($step / $totalSteps) * 100 }}%" 
                                        aria-valuenow="{{ $step }}" aria-valuemin="1" aria-valuemax="{{ $totalSteps }}"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-xs fw-bold {{ $step >= 1 ? 'text-primary' : 'text-muted' }}">Identitas</span>
                                    <span class="text-xs fw-bold {{ $step >= 2 ? 'text-primary' : 'text-muted' }}">Orang Tua</span>
                                    <span class="text-xs fw-bold {{ $step >= 3 ? 'text-primary' : 'text-muted' }}">Sekolah</span>
                                    <span class="text-xs fw-bold {{ $step >= 4 ? 'text-primary' : 'text-muted' }}">Alamat</span>
                                    <span class="text-xs fw-bold {{ $step >= 5 ? 'text-primary' : 'text-muted' }}">Tambahan</span>
                                    <span class="text-xs fw-bold {{ $step >= 6 ? 'text-primary' : 'text-muted' }}">Akun</span>
                                </div>
                            </div>

                            <form wire:submit.prevent="register">
                                <!-- Step 1: Identitas -->
                                @if ($step == 1)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 1: Identitas Siswa</h5>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label required">Nama Lengkap (Sesuai Ijazah/Akte)</label>
                                                <input type="text" class="form-control @error('nama') is-invalid @enderror" wire:model.blur="nama" placeholder="Masukkan nama lengkap">
                                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">NISN</label>
                                                <input type="number" class="form-control @error('nisn') is-invalid @enderror" wire:model.blur="nisn" placeholder="10 digit NISN">
                                                @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">NIK</label>
                                                <input type="number" class="form-control @error('nik') is-invalid @enderror" wire:model.blur="nik" placeholder="16 digit NIK">
                                                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Jenis Kelamin</label>
                                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" wire:model="jenis_kelamin">
                                                    <option value="">Pilih...</option>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                                @error('jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Tempat Lahir</label>
                                                <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" wire:model="tempat_lahir" placeholder="Kota kelahiran">
                                                @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Tanggal Lahir</label>
                                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" wire:model="tanggal_lahir">
                                                @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kebutuhan Khusus</label>
                                                <input type="text" class="form-control @error('kebutuhan_khusus') is-invalid @enderror" wire:model="kebutuhan_khusus" placeholder="Jika ada">
                                                @error('kebutuhan_khusus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Step 2: Orang Tua -->
                                @if ($step == 2)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 2: Data Orang Tua & Wali</h5>
                                        
                                        <!-- Ibu -->
                                        <h6 class="text-primary mt-4 mb-3">Data Ibu Kandung</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label required">Nama Ibu Kandung</label>
                                                <input type="text" class="form-control @error('nama_ibu_kandung') is-invalid @enderror" wire:model="nama_ibu_kandung" placeholder="Nama ibu kandung sesuai KK">
                                                @error('nama_ibu_kandung') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Pekerjaan Ibu</label>
                                                <input type="text" class="form-control @error('pekerjaan_ibu') is-invalid @enderror" wire:model="pekerjaan_ibu" placeholder="Pekerjaan Ibu">
                                                @error('pekerjaan_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Penghasilan Ibu</label>
                                                <select class="form-select @error('penghasilan_ibu') is-invalid @enderror" wire:model="penghasilan_ibu">
                                                    <option value="">Pilih Penghasilan...</option>
                                                    <option value="Tidak Berpenghasilan">Tidak Berpenghasilan</option>
                                                    <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                    <option value="Rp. 500.000 - Rp. 999.999">Rp. 500.000 - Rp. 999.999</option>
                                                    <option value="Rp. 1.000.000 - Rp. 1.999.999">Rp. 1.000.000 - Rp. 1.999.999</option>
                                                    <option value="Rp. 2.000.000 - Rp. 4.999.999">Rp. 2.000.000 - Rp. 4.999.999</option>
                                                    <option value="Rp. 5.000.000 - Rp. 20.000.000">Rp. 5.000.000 - Rp. 20.000.000</option>
                                                    <option value="Lebih dari Rp. 20.000.000">Lebih dari Rp. 20.000.000</option>
                                                </select>
                                                @error('penghasilan_ibu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <!-- Ayah -->
                                        <h6 class="text-primary mt-4 mb-3">Data Ayah</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Nama Ayah</label>
                                                <input type="text" class="form-control @error('nama_ayah') is-invalid @enderror" wire:model="nama_ayah">
                                                @error('nama_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pekerjaan Ayah</label>
                                                <input type="text" class="form-control @error('pekerjaan_ayah') is-invalid @enderror" wire:model="pekerjaan_ayah">
                                                @error('pekerjaan_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Penghasilan Ayah</label>
                                                <select class="form-select @error('penghasilan_ayah') is-invalid @enderror" wire:model="penghasilan_ayah">
                                                    <option value="">Pilih Penghasilan...</option>
                                                    <option value="Tidak Berpenghasilan">Tidak Berpenghasilan</option>
                                                    <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                    <option value="Rp. 500.000 - Rp. 999.999">Rp. 500.000 - Rp. 999.999</option>
                                                    <option value="Rp. 1.000.000 - Rp. 1.999.999">Rp. 1.000.000 - Rp. 1.999.999</option>
                                                    <option value="Rp. 2.000.000 - Rp. 4.999.999">Rp. 2.000.000 - Rp. 4.999.999</option>
                                                    <option value="Rp. 5.000.000 - Rp. 20.000.000">Rp. 5.000.000 - Rp. 20.000.000</option>
                                                    <option value="Lebih dari Rp. 20.000.000">Lebih dari Rp. 20.000.000</option>
                                                </select>
                                                @error('penghasilan_ayah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <!-- Wali -->
                                        <h6 class="text-primary mt-4 mb-3">Data Wali (Opsional)</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Nama Wali</label>
                                                <input type="text" class="form-control @error('nama_wali') is-invalid @enderror" wire:model="nama_wali">
                                                @error('nama_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pekerjaan Wali</label>
                                                <input type="text" class="form-control @error('pekerjaan_wali') is-invalid @enderror" wire:model="pekerjaan_wali">
                                                @error('pekerjaan_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Penghasilan Wali</label>
                                                <select class="form-select @error('penghasilan_wali') is-invalid @enderror" wire:model="penghasilan_wali">
                                                    <option value="">Pilih Penghasilan...</option>
                                                    <option value="Tidak Berpenghasilan">Tidak Berpenghasilan</option>
                                                    <option value="Kurang dari Rp. 500.000">Kurang dari Rp. 500.000</option>
                                                    <option value="Rp. 500.000 - Rp. 999.999">Rp. 500.000 - Rp. 999.999</option>
                                                    <option value="Rp. 1.000.000 - Rp. 1.999.999">Rp. 1.000.000 - Rp. 1.999.999</option>
                                                    <option value="Rp. 2.000.000 - Rp. 4.999.999">Rp. 2.000.000 - Rp. 4.999.999</option>
                                                    <option value="Rp. 5.000.000 - Rp. 20.000.000">Rp. 5.000.000 - Rp. 20.000.000</option>
                                                    <option value="Lebih dari Rp. 20.000.000">Lebih dari Rp. 20.000.000</option>
                                                </select>
                                                @error('penghasilan_wali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                    </div>
                                @endif

                                <!-- Step 3: Sekolah Asal -->
                                @if ($step == 3)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 3: Sekolah Asal</h5>
                                        <div class="alert alert-info">
                                            <i class="fi fi-rs-info me-2"></i> Masukkan nama sekolah asal Anda.
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label required">Nama Sekolah Asal</label>
                                                <input type="text" class="form-control @error('sekolah_asal_text') is-invalid @enderror" wire:model="sekolah_asal_text" placeholder="Contoh: SD Negeri 1 Cianjur (Luar Wilayah)">
                                                @error('sekolah_asal_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">NPSN Sekolah Asal (Opsional)</label>
                                                <input type="number" class="form-control @error('npsn_sekolah_asal') is-invalid @enderror" wire:model="npsn_sekolah_asal" placeholder="8 digit NPSN">
                                                @error('npsn_sekolah_asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Step 4: Alamat -->
                                @if ($step == 4)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 4: Alamat Domisili</h5>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label required">Alamat Jalan</label>
                                                <textarea class="form-control @error('alamat_jalan') is-invalid @enderror" wire:model="alamat_jalan" rows="2" placeholder="Nama jalan, gang, nomor rumah"></textarea>
                                                @error('alamat_jalan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="form-label required">RT</label>
                                                <input type="number" class="form-control @error('rt') is-invalid @enderror" wire:model="rt" placeholder="001">
                                                @error('rt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <label class="form-label required">RW</label>
                                                <input type="number" class="form-control @error('rw') is-invalid @enderror" wire:model="rw" placeholder="001">
                                                @error('rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Nama Dusun</label>
                                                <input type="text" class="form-control @error('nama_dusun') is-invalid @enderror" wire:model="nama_dusun" placeholder="Nama Dusun/Kampung">
                                                @error('nama_dusun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Desa / Kelurahan</label>
                                                <input type="text" class="form-control @error('desa_kelurahan') is-invalid @enderror" wire:model="desa_kelurahan">
                                                @error('desa_kelurahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label required">Kecamatan</label>
                                                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" wire:model="kecamatan">
                                                @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Lintang (Latitude)</label>
                                                <input type="text" class="form-control @error('lintang') is-invalid @enderror" wire:model="lintang" placeholder="-6.123456">
                                                @error('lintang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Bujur (Longitude)</label>
                                                <input type="text" class="form-control @error('bujur') is-invalid @enderror" wire:model="bujur" placeholder="107.123456">
                                                @error('bujur') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">No. Handphone/WA (Aktif)</label>
                                                <input type="text" class="form-control @error('no_handphone') is-invalid @enderror" wire:model="no_handphone" placeholder="Contoh: 08123456789">
                                                @error('no_handphone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Step 5: Data Tambahan -->
                                @if ($step == 5)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 5: Data KIP/PKH (Jika Ada)</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Apakah Penerima PIP?</label>
                                                <select class="form-select @error('flag_pip') is-invalid @enderror" wire:model="flag_pip">
                                                    <option value="">Pilih...</option>
                                                    <option value="Ya">Ya</option>
                                                    <option value="Tidak">Tidak</option>
                                                </select>
                                                @error('flag_pip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nomor KIP (Kartu Indonesia Pintar)</label>
                                                <input type="text" class="form-control @error('no_KIP') is-invalid @enderror" wire:model="no_KIP" placeholder="---">
                                                @error('no_KIP') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nomor PKH (Program Keluarga Harapan)</label>
                                                <input type="text" class="form-control @error('no_pkh') is-invalid @enderror" wire:model="no_pkh" placeholder="---">
                                                @error('no_pkh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Step 6: Akun -->
                                @if ($step == 6)
                                    <div class="mb-4">
                                        <h5 class="mb-3">Langkah 6: Buat Akun</h5>
                                        <div class="alert alert-warning">
                                            <i class="fi fi-rs-triangle-warning me-2"></i> Pastikan password yang Anda buat mudah diingat! Username Anda adalah <strong>NISN</strong> yang Anda masukkan.
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label required">Password</label>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="Minimal 8 karakter">
                                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label required">Konfirmasi Password</label>
                                                <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Ulangi password">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    @if ($step > 1)
                                        <button type="button" class="btn btn-light" wire:click="previousStep">
                                            <i class="fi fi-rr-arrow-left me-2"></i> Sebelumnya
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-light">
                                            <i class="fi fi-rr-sign-in-alt me-2"></i> Ke Login
                                        </a>
                                    @endif

                                    @if ($step < $totalSteps)
                                        <button type="button" class="btn btn-primary" wire:click="nextStep">
                                            Selanjutnya <i class="fi fi-rr-arrow-right ms-2"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success">
                                            <i class="fi fi-rr-check-circle me-2"></i> Daftar Sekarang
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
