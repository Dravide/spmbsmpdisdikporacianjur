<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pendaftaran Peserta Didik Baru</h4>
            <p class="text-muted mb-0">Lengkapi data pendaftaran Anda dalam 5 langkah mudah.</p>
        </div>
        <div>
             <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">
                <i class="fi fi-rr-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if($isSubmitted && $registrationData)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fi fi-rr-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="fw-bold">Pendaftaran Berhasil Dikirim</h4>
                    <p class="text-muted mb-2">Nomor Pendaftaran: <strong class="fs-5">{{ $registrationData->nomor_pendaftaran }}</strong></p>
                    
                    @php
                        $statusColors = [
                            'draft' => 'secondary',
                            'submitted' => 'primary',
                            'verified' => 'info',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                        ];
                        $statusLabels = [
                            'draft' => 'Draft',
                            'submitted' => 'Menunggu Verifikasi',
                            'verified' => 'Terverifikasi',
                            'accepted' => 'Diterima',
                            'rejected' => 'Ditolak',
                        ];
                    @endphp
                    <div class="badge bg-{{ $statusColors[$registrationData->status] ?? 'secondary' }} fs-6 px-3 py-2">
                        {{ $statusLabels[$registrationData->status] ?? ucfirst($registrationData->status) }}
                    </div>
                </div>

                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fi fi-rr-lock me-2 fs-5"></i>
                    <div>
                        <strong>Data Terkunci!</strong> Pendaftaran yang sudah dikirim tidak dapat diubah kembali.
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Data Peserta Didik -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3 border-bottom pb-2">
                                    <i class="fi fi-rr-user me-2"></i> Data Peserta Didik
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Nama Lengkap</td>
                                        <td class="fw-bold">{{ $userData->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">NISN</td>
                                        <td class="fw-bold">{{ $userData->nisn ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Sekolah Asal</td>
                                        <td class="fw-bold">{{ $userData->sekolah->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tempat, Tgl Lahir</td>
                                        <td class="fw-bold">
                                            {{ $userData->tempat_lahir ?? '-' }}, 
                                            {{ $userData->tanggal_lahir ? \Carbon\Carbon::parse($userData->tanggal_lahir)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Alamat</td>
                                        <td class="fw-bold">{{ $userData->alamat_jalan ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pendaftaran -->
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3 border-bottom pb-2">
                                    <i class="fi fi-rr-clipboard-list me-2"></i> Data Pendaftaran
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Sekolah Tujuan</td>
                                        <td class="fw-bold">{{ $registrationData->sekolah->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jalur</td>
                                        <td class="fw-bold">{{ $registrationData->jalur->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tanggal Daftar</td>
                                        <td class="fw-bold">{{ $registrationData->tanggal_daftar?->format('d F Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jarak</td>
                                        <td class="fw-bold">
                                            @if($registrationData->jarak_meter)
                                                {{ number_format($registrationData->jarak_meter / 1000, 2) }} KM
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Koordinat</td>
                                        <td class="fw-bold">
                                            @if($registrationData->latitude && $registrationData->longitude)
                                                {{ number_format($registrationData->latitude, 6) }}, {{ number_format($registrationData->longitude, 6) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Berkas -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="card-title mb-3 border-bottom pb-2">
                                    <i class="fi fi-rr-file me-2"></i> Daftar Berkas
                                </h6>
                                @if($registrationData->berkas && $registrationData->berkas->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-white">
                                                <tr>
                                                    <th>Jenis Berkas</th>
                                                    <th>Nama File</th>
                                                    <th>Status</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($registrationData->berkas as $file)
                                                    @php
                                                        $berkasStatusColors = [
                                                            'pending' => 'secondary',
                                                            'approved' => 'success',
                                                            'revision' => 'warning',
                                                            'rejected' => 'danger',
                                                        ];
                                                        $berkasStatusLabels = [
                                                            'pending' => 'Menunggu Verifikasi',
                                                            'approved' => 'Disetujui',
                                                            'revision' => 'Perlu Perbaikan',
                                                            'rejected' => 'Ditolak',
                                                        ];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <i class="fi fi-rr-document me-1"></i>
                                                            {{ $file->berkas->nama ?? 'Berkas' }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="text-primary">
                                                                {{ $file->nama_file_asli ?? '-' }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $berkasStatusColors[$file->status_berkas ?? 'pending'] }}">
                                                                {{ $berkasStatusLabels[$file->status_berkas ?? 'pending'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($file->catatan_verifikasi)
                                                                <span class="text-muted" title="{{ $file->catatan_verifikasi }}">
                                                                    {{ Str::limit($file->catatan_verifikasi, 50) }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fi fi-rr-inbox d-block mb-2 fs-3"></i>
                                        Tidak ada berkas yang diunggah.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Pendaftaran (if any) -->
                    @if($registrationData->catatan)
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <h6 class="alert-heading"><i class="fi fi-rr-comment me-2"></i> Catatan dari Panitia</h6>
                                {{ $registrationData->catatan }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
                        <i class="fi fi-rr-apps me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    @else
    <!-- Wizard Progress -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="position-relative m-4">
                <div class="progress" style="height: 2px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ (($step - 1) / ($totalSteps - 1)) * 100 }}%;"></div>
                </div>
                
                @for ($i = 1; $i <= $totalSteps; $i++)
                    <button type="button" 
                        class="position-absolute top-0 translate-middle btn btn-sm rounded-pill {{ $step >= $i ? 'btn-primary' : 'btn-secondary' }}" 
                        style="left: {{ (($i - 1) / ($totalSteps - 1)) * 100 }}%; width: 2rem; height:2rem;">
                        {{ $i }}
                    </button>
                    <!-- Label for Step -->
                    <div class="position-absolute" style="top: 30px; left: {{ (($i - 1) / ($totalSteps - 1)) * 100 }}%; transform: translateX(-50%); font-size: 0.8rem; white-space: nowrap;">
                        @if($i==1) Data Diri
                        @elseif($i==2) Pilih Sekolah
                        @elseif($i==3) Lokasi
                        @elseif($i==4) Jalur
                        @elseif($i==5) Berkas
                        @elseif($i==6) Validasi
                        @endif
                    </div>
                @endfor
            </div>
            <div class="mt-5"></div>
        </div>
    </div>

    <!-- Wizard Content -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white p-4 d-flex justify-content-between border-bottom">
            @if($step > 1)
                <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">
                    <i class="fi fi-rr-angle-left me-1"></i> Sebelumnya
                </button>
            @else
                <div></div> 
            @endif

            @if($step < $totalSteps)
                <button type="button" class="btn btn-primary" onclick="confirmNext()">
                    Selanjutnya <i class="fi fi-rr-angle-right ms-1"></i>
                </button>
            @else
                <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                    <i class="fi fi-rr-paper-plane me-1"></i> Kirim
                </button>
            @endif
        </div>
        <div class="card-body p-4">
            
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fi fi-rr-exclamation me-2"></i>
                    <strong>Terjadi Kesalahan!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Step 1: Konfirmasi Data Diri -->
            @if ($step == 1)
                <h5 class="mb-3">Langkah 1: Konfirmasi Data Diri</h5>
                <p class="text-muted">Sebelum melanjutkan, mohon periksa kembali data diri Anda.</p>

                @error('step1')
                    <div class="alert alert-danger d-flex align-items-center mb-4">
                        <i class="fi fi-rr-exclamation-triangle fs-4 me-3"></i>
                        <div>
                            <strong>Data Belum Lengkap!</strong>
                            <div class="small">{{ $message }}</div>
                        </div>
                    </div>
                @enderror

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <!-- Identitas -->
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Identitas Peserta Didik</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="small text-muted d-block">Nama Lengkap</label>
                                <span class="fw-bold fs-5 {{ isset($missingFields['nama']) ? 'text-danger' : '' }}">{{ $userData->nama ?? '-' }}
                                    @if(isset($missingFields['nama'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">NISN</label>
                                <span class="fw-bold {{ isset($missingFields['nisn']) ? 'text-danger' : '' }}">{{ $userData->nisn ?? '-' }}
                                    @if(isset($missingFields['nisn'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">NIK</label>
                                <span class="fw-bold {{ isset($missingFields['nik']) ? 'text-danger' : '' }}">{{ $userData->nik ?? '-' }}
                                    @if(isset($missingFields['nik'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">No. Kartu Keluarga</label>
                                <span class="fw-bold {{ isset($missingFields['no_kk']) ? 'text-danger' : '' }}">{{ $userData->no_kk ?? '-' }}
                                    @if(isset($missingFields['no_kk'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">Jenis Kelamin</label>
                                <span class="fw-bold {{ isset($missingFields['jenis_kelamin']) ? 'text-danger' : '' }}">
                                    {{ $userData->jenis_kelamin == 'L' ? 'Laki-laki' : ($userData->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                    @if(isset($missingFields['jenis_kelamin'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">Tempat, Tanggal Lahir</label>
                                <span class="fw-bold {{ (isset($missingFields['tempat_lahir']) || isset($missingFields['tanggal_lahir'])) ? 'text-danger' : '' }}">
                                    {{ $userData->tempat_lahir ?? '-' }}, 
                                    {{ $userData->tanggal_lahir ? \Carbon\Carbon::parse($userData->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '-' }}
                                    @if(isset($missingFields['tempat_lahir']) || isset($missingFields['tanggal_lahir'])) <small class="text-danger">(Belum Lengkap)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">Sekolah Asal</label>
                                <span class="fw-bold">{{ $userData->sekolah->nama ?? '-' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted d-block">Kebutuhan Khusus</label>
                                <span class="fw-bold">{{ $userData->kebutuhan_khusus ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Alamat Tempat Tinggal</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="small text-muted d-block">Alamat Jalan</label>
                                <span class="fw-bold {{ isset($missingFields['alamat_jalan']) ? 'text-danger' : '' }}">{{ $userData->alamat_jalan ?? '-' }}
                                    @if(isset($missingFields['alamat_jalan'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">Desa / Kelurahan</label>
                                <span class="fw-bold">{{ $userData->desa_kelurahan ?? '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">Kecamatan</label>
                                <span class="fw-bold {{ isset($missingFields['kecamatan']) ? 'text-danger' : '' }}">{{ $userData->kecamatan ?? '-' }}
                                    @if(isset($missingFields['kecamatan'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">Dusun</label>
                                <span class="fw-bold">{{ $userData->nama_dusun ?? '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">RT / RW</label>
                                <span class="fw-bold">{{ $userData->rt ?? '-' }} / {{ $userData->rw ?? '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="small text-muted d-block">Koordinat (Awal)</label>
                                <span class="fw-bold {{ (isset($missingFields['lintang']) || isset($missingFields['bujur'])) ? 'text-danger' : '' }}">
                                    {{ $userData->lintang ?? '-' }}, {{ $userData->bujur ?? '-' }}
                                    @if(isset($missingFields['lintang']) || isset($missingFields['bujur'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                </span>
                            </div>
                        </div>

                        <!-- Data Orang Tua -->
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Data Orang Tua / Wali</h6>
                        <div class="row g-4 mb-4">
                            <!-- Ibu -->
                            <div class="col-md-4">
                                <div class="bg-body-secondary p-3 rounded h-100">
                                    <strong class="d-block mb-3 text-secondary text-uppercase small ls-1">Ibu Kandung</strong>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Nama</label>
                                        <span class="fw-bold {{ isset($missingFields['nama_ibu_kandung']) ? 'text-danger' : '' }}">{{ $userData->nama_ibu_kandung ?? '-' }}
                                            @if(isset($missingFields['nama_ibu_kandung'])) <small class="text-danger">(Belum Diisi)</small> @endif
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Pekerjaan</label>
                                        <span class="fw-bold">{{ $userData->pekerjaan_ibu ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Penghasilan</label>
                                        <span class="fw-bold">{{ $userData->penghasilan_ibu ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Ayah -->
                            <div class="col-md-4">
                                <div class="bg-body-secondary p-3 rounded h-100">
                                    <strong class="d-block mb-3 text-secondary text-uppercase small ls-1">Ayah</strong>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Nama</label>
                                        <span class="fw-bold">{{ $userData->nama_ayah ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Pekerjaan</label>
                                        <span class="fw-bold">{{ $userData->pekerjaan_ayah ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Penghasilan</label>
                                        <span class="fw-bold">{{ $userData->penghasilan_ayah ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Wali -->
                            <div class="col-md-4">
                                <div class="bg-body-secondary p-3 rounded h-100">
                                    <strong class="d-block mb-3 text-secondary text-uppercase small ls-1">Wali</strong>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Nama</label>
                                        <span class="fw-bold">{{ $userData->nama_wali ?? '-' }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted d-block">Pekerjaan</label>
                                        <span class="fw-bold">{{ $userData->pekerjaan_wali ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block">Penghasilan</label>
                                        <span class="fw-bold">{{ $userData->penghasilan_wali ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lainnya -->
                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Data Lainnya</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small text-muted d-block">No. KIP</label>
                                <span class="fw-bold">{{ $userData->no_KIP ?? '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">No. PKH</label>
                                <span class="fw-bold">{{ $userData->no_pkh ?? '-' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted d-block">Penerima PIP (Flag)</label>
                                <span class="fw-bold">{{ $userData->flag_pip == 1 ? 'Ya' : 'Tidak' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mb-4">
                    <i class="fi fi-rr-info me-2"></i> Jika terdapat kesalahan data, silakan hubungi Operator Sekolah Asal Anda.
                </div>
            @endif

            <!-- Step 2: Pilih Sekolah -->
            @if ($step == 2)
                <h5 class="mb-3">Langkah 2: Pilih Sekolah Tujuan</h5>
                
                <div class="mb-3">
                    <input type="text" class="form-control form-control-lg" placeholder="Cari nama sekolah SMP..." wire:model.live.debounce.300ms="searchSekolah">
                </div>

                @if($selectedSekolahId)
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fi fi-rr-check-circle fs-4 me-3"></i>
                        <div>
                            <strong>Sekolah Terpilih:</strong>
                            <div class="fs-5">{{ $selectedSekolahName }}</div>
                        </div>
                    </div>
                @endif

                <div class="list-group">
                    @forelse ($sekolahList as $sekolah)
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedSekolahId == $sekolah->sekolah_id ? 'active' : '' }}"
                            wire:click="selectSekolah('{{ $sekolah->sekolah_id }}', '{{ $sekolah->nama }}', {{ $sekolah->lintang ?? 0 }}, {{ $sekolah->bujur ?? 0 }})">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="mb-0">{{ $sekolah->nama }}</h6>
                                    @if($sekolah->mode_spmb == 'Full Online')
                                        <span class="badge bg-success-subtle text-success" style="font-size: 0.7rem;">
                                            <i class="fi fi-rr-globe me-1"></i>Full Online
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning" style="font-size: 0.7rem;">
                                            <i class="fi fi-rr-school me-1"></i>Semi Online
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted {{ $selectedSekolahId == $sekolah->sekolah_id ? 'text-white-50' : '' }}">{{ $sekolah->desa_kelurahan ?? 'Alamat tidak tersedia' }}</small>
                            </div>
                            @if($selectedSekolahId == $sekolah->sekolah_id)
                                <i class="fi fi-rr-check"></i>
                            @endif
                        </button>
                    @empty
                        <div class="text-center py-4 text-muted">
                            @if($searchSekolah)
                                Tidak ada sekolah yang cocok dengan pencarian.
                            @else
                                Ketik nama sekolah untuk mencari.
                            @endif
                        </div>
                    @endforelse
                </div>
                @error('selectedSekolahId') <div class="text-danger mt-2">{{ $message }}</div> @enderror
            @endif

            <!-- Step 3: Lokasi -->
            @if ($step == 3)
                <h5 class="mb-3">Langkah 3: Titik Koordinat Rumah</h5>
                <p class="text-muted">Pastikan titik koordinat sesuai dengan lokasi tempat tinggal Anda untuk perhitungan jarak zonasi.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Lintang (Latitude)</label>
                        <input type="text" class="form-control" wire:model="latitude" readonly>
                        @error('latitude') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bujur (Longitude)</label>
                        <input type="text" class="form-control" wire:model="longitude" readonly>
                        @error('longitude') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if($distance)
                    <div class="alert alert-info mt-3">
                        <i class="fi fi-rr-map-marker me-2"></i> Jarak ke <strong>{{ $selectedSekolahName }}</strong>: <strong>{{ number_format($distance / 1000, 2) }} KM</strong> ({{ $distance }} meter)
                    </div>
                @endif

                <div id="map" class="mt-3 rounded border" style="height: 600px;" wire:ignore></div>
                <div class="mt-2 text-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation()">
                        <i class="fi fi-rr-crosshairs me-1"></i> Gunakan Lokasi Saat Ini
                    </button>
                </div>
            @endif

            <!-- Step 4: Pilih Jalur -->
            <!-- Step 4: Pilih Jalur -->
            <!-- Step 4: Pilih Jalur -->
            @if ($step == 4)
                <div class="text-center mb-3">
                    <h5 class="fw-bold">Pilih Jalur Pendaftaran</h5>
                    <p class="text-muted small">Pilih jalur pendaftaran yang sesuai dengan kriteria Anda.</p>
                </div>
                
                <div class="row g-3 justify-content-center">
                    @forelse($jalurList as $jalur)
                        <div class="col-md-6 col-lg-4">
                            <label class="card h-100 card-action action-border-primary {{ $selectedJalurId == $jalur->id ? 'action-active border-primary shadow-sm' : 'border' }} cursor-pointer position-relative transition-all" style="cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="jalur" value="{{ $jalur->id }}" wire:model.live="selectedJalurId" class="d-none">
                                
                                <div class="card-body p-3 text-center d-flex flex-column align-items-center">
                                    <!-- Dynamic Icon based on Jalur Name -->
                                    @php
                                        $iconClass = 'fi-rr-road'; // default
                                        $bgClass = 'bg-primary-subtle text-primary';
                                        
                                        $lowerName = strtolower($jalur->nama);
                                        if (str_contains($lowerName, 'zonasi')) {
                                            $iconClass = 'fi-rr-map-marker-home';
                                        } elseif (str_contains($lowerName, 'prestasi')) {
                                            $iconClass = 'fi-rr-trophy';
                                            $bgClass = 'bg-warning-subtle text-warning';
                                        } elseif (str_contains($lowerName, 'afirmasi')) {
                                            $iconClass = 'fi-rr-heart';
                                            $bgClass = 'bg-danger-subtle text-danger';
                                        } elseif (str_contains($lowerName, 'pindah')) {
                                            $iconClass = 'fi-rr-briefcase';
                                            $bgClass = 'bg-info-subtle text-info';
                                        }
                                        
                                        if($selectedJalurId == $jalur->id) {
                                            $bgClass = 'bg-primary text-white';
                                        }
                                    @endphp

                                    <div class="avatar avatar-lg rounded-circle mb-2 {{ $bgClass }} d-flex align-items-center justify-content-center transition-colors">
                                        <i class="fi {{ $iconClass }} fs-4"></i>
                                    </div>

                                    <h6 class="fw-bold mb-1">{{ $jalur->nama }}</h6>
                                    <p class="text-muted small mb-2 flex-grow-1" style="font-size: 0.8rem;">{{ $jalur->deskripsi }}</p>

                                    @if($jalur->kuota)
                                        <span class="badge {{ $selectedJalurId == $jalur->id ? 'bg-primary-subtle text-primary border border-primary' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-2 py-1 mt-auto" style="font-size: 0.7rem;">
                                            <i class="fi fi-rr-users-alt me-1"></i> Kuota: {{ $jalur->kuota }}
                                        </span>
                                    @endif
                                </div>

                                @if($selectedJalurId == $jalur->id)
                                    <div class="position-absolute top-0 end-0 mt-2 me-2">
                                        <div class="bg-primary text-white rounded-circle p-1 d-flex shadow-sm" style="width: 20px; height: 20px; align-items: center; justify-content: center;">
                                            <i class="fi fi-br-check" style="font-size: 0.6rem;"></i>
                                        </div>
                                    </div>
                                @endif
                            </label>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <div class="mb-2">
                                <div class="avatar avatar-xl bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto">
                                    <i class="fi fi-rr-calendar-clock fs-2 text-muted"></i>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark">Belum Ada Jalur Dibuka</h6>
                            <p class="text-muted small">Saat ini belum ada jalur pendaftaran yang tersedia/aktif.</p>
                        </div>
                    @endforelse
                </div>
                
                @error('selectedJalurId') 
                    <div class="text-center mt-3">
                        <div class="alert alert-danger d-inline-block px-3 py-1 mb-0 small">
                             <i class="fi fi-rr-info me-1"></i> {{ $message }}
                        </div>
                    </div>
                @enderror
                
                @if($requiredBerkas)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold mb-2 d-flex align-items-center small">
                            <i class="fi fi-rr-document-signed me-2 text-primary"></i> 
                            Dokumen Persyaratan:
                        </h6>
                        <div class="row g-2">
                            @foreach($requiredBerkas as $berkas)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center bg-white border rounded p-2 h-100">
                                        <div class="avatar avatar-xs {{ $berkas->is_required ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-circle me-2 flex-shrink-0 d-flex align-items-center justify-content-center">
                                            <i class="fi {{ $berkas->is_required ? 'fi-rr-exclamation' : 'fi-rr-check' }}" style="font-size: 0.7rem;"></i>
                                        </div>
                                        <div class="flex-grow-1" style="line-height: 1.2;">
                                            <div class="fw-semibold text-dark small" style="font-size: 0.85rem;">{{ $berkas->nama }}</div>
                                            <small class="{{ $berkas->is_required ? 'text-danger' : 'text-success' }}" style="font-size: 0.7rem;">
                                                {{ $berkas->is_required ? 'Wajib' : 'Opsional' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Step 5: Upload Berkas -->
            @if ($step == 5)
                <h5 class="mb-3">Langkah 5: Upload Dokumen</h5>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fi fi-rr-info fs-5 me-2"></i>
                    <small>Pastikan dokumen terbaca jelas. Format: PDF/JPG/PNG. Maksimal ukuran sesuai ketentuan per dokumen.</small>
                </div>

                @if(count($requiredBerkas) > 0)
                    @php
                        $berkasGrouped = collect($requiredBerkas)->groupBy(function($item) {
                            return $item->jenis ?? 'Lainnya';
                        });
                        $jenisConfig = [
                            'Berkas Umum' => ['icon' => 'fi-rr-document', 'color' => 'primary', 'title' => 'Berkas Umum', 'desc' => 'Dokumen wajib untuk semua jalur pendaftaran'],
                            'Berkas Khusus' => ['icon' => 'fi-rr-star', 'color' => 'warning', 'title' => 'Berkas Khusus Jalur', 'desc' => 'Dokumen khusus sesuai jalur yang dipilih'],
                            'Berkas Tambahan' => ['icon' => 'fi-rr-add-document', 'color' => 'info', 'title' => 'Berkas Tambahan', 'desc' => 'Dokumen pendukung (opsional)'],
                            'Lainnya' => ['icon' => 'fi-rr-folder', 'color' => 'secondary', 'title' => 'Berkas Lainnya', 'desc' => 'Dokumen persyaratan tambahan'],
                        ];
                        $jenisOrder = ['Berkas Umum', 'Berkas Khusus', 'Berkas Tambahan', 'Lainnya'];
                    @endphp

                    @foreach($jenisOrder as $jenis)
                        @if($berkasGrouped->has($jenis) && $berkasGrouped[$jenis]->count() > 0)
                            @php $config = $jenisConfig[$jenis]; @endphp
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }} p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fi {{ $config['icon'] }} fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $config['title'] }}</h6>
                                        <small class="text-muted">{{ $config['desc'] }}</small>
                                    </div>
                                    <span class="badge bg-{{ $config['color'] }}-subtle text-{{ $config['color'] }} ms-auto">
                                        {{ $berkasGrouped[$jenis]->count() }} Berkas
                                    </span>
                                </div>
                                
                                <div class="row g-3">
                                    @foreach($berkasGrouped[$jenis] as $berkas)
                                        <div class="col-md-6">
                                            @include('livewire.student.partials.berkas-card', [
                                                'berkas' => $berkas,
                                                'existingFile' => $existingFiles[$berkas->id] ?? null,
                                                'berkasFiles' => $berkasFiles,
                                                'uploadedBerkasData' => $uploadedBerkasData
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fi fi-rr-document-signed fs-1 mb-3 d-block"></i>
                        Tidak ada berkas yang perlu diunggah untuk jalur ini.
                    </div>
                @endif
            @endif

            <!-- Step 6: Validasi -->
            @if ($step == 6)
                <div class="text-center mb-4">
                    <h5 class="fw-bold">Langkah 6: Validasi & Kirim</h5>
                    <p class="text-muted">Mohon periksa kembali seluruh data sebelum mengirim pendaftaran.</p>
                </div>
                
                <div class="accordion mb-4" id="accordionReview">
                    
                    <!-- 1. Pilihan Pendaftaran -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reviewPendaftaran" aria-expanded="true">
                                <i class="fi fi-rr-school me-2 text-primary"></i> Pilihan Pendaftaran
                            </button>
                        </h2>
                        <div id="reviewPendaftaran" class="accordion-collapse collapse show" data-bs-parent="#accordionReview">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block uppercase">Sekolah Tujuan</small>
                                        <div class="fw-bold fs-5 text-primary">{{ $selectedSekolah->nama ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block uppercase">Jalur Pendaftaran</small>
                                        <div class="fw-bold fs-5">{{ $selectedJalur->nama ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block uppercase">Jarak Tempuh</small>
                                        <div class="fw-bold">{{ $distance ? number_format($distance / 1000, 2) . ' KM' : '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block uppercase">Koordinat Anda</small>
                                        <div class="fw-bold font-monospace">{{ $latitude ?? '-' }}, {{ $longitude ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Data Pribadi -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reviewPribadi">
                                <i class="fi fi-rr-user me-2 text-primary"></i> Data Pribadi & Alamat
                            </button>
                        </h2>
                        <div id="reviewPribadi" class="accordion-collapse collapse" data-bs-parent="#accordionReview">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Nama Lengkap</small>
                                        <strong class="text-dark">{{ $userData->nama ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">NISN</small>
                                        <strong>{{ $userData->nisn ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">NIK</small>
                                        <strong>{{ $userData->nik ?? '-' }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Tempat, Tanggal Lahir</small>
                                        <strong>{{ $userData->tempat_lahir ?? '-' }}, {{ $userData->tanggal_lahir ? \Carbon\Carbon::parse($userData->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Jenis Kelamin</small>
                                        <strong>{{ $userData->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                                    </div>
                                    <div class="col-12 border-top pt-2 mt-2">
                                        <small class="text-muted d-block">Alamat Lengkap</small>
                                        <strong>{{ $userData->alamat_jalan ?? '-' }}</strong>
                                        <div class="text-muted small">
                                            Desa {{ $userData->desa_kelurahan ?? '-' }}, Kec. {{ $userData->kecamatan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Berkas Persyaratan -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#reviewBerkas">
                                <i class="fi fi-rr-document-signed me-2 text-primary"></i> Berkas Persyaratan
                            </button>
                        </h2>
                        <div id="reviewBerkas" class="accordion-collapse collapse show" data-bs-parent="#accordionReview">
                            <div class="accordion-body p-0">
                                @if(count($requiredBerkas) > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($requiredBerkas as $berkas)
                                            @php
                                                $uploaded = isset($existingFiles[$berkas->id]);
                                                $file = $existingFiles[$berkas->id] ?? null;
                                            @endphp
                                            <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm {{ $uploaded ? 'bg-success-subtle text-success' : ($berkas->is_required ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} rounded-circle me-3 flex-shrink-0 d-flex align-items-center justify-content-center">
                                                        <i class="fi {{ $uploaded ? 'fi-rr-check' : 'fi-rr-cross' }}"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold {{ $uploaded ? 'text-dark' : 'text-muted' }}">{{ $berkas->nama }}</div>
                                                        @if($uploaded)
                                                            <small class="text-success"><i class="fi fi-rr-file-check me-1"></i>Telah diunggah</small>
                                                        @else
                                                            <small class="{{ $berkas->is_required ? 'text-danger' : 'text-warning' }}">
                                                                {{ $berkas->is_required ? 'Belum diunggah (Wajib)' : 'Belum diunggah (Opsional)' }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($uploaded)
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="openPdfPreview('{{ asset('storage/' . $file->file_path) }}', '{{ $berkas->nama }}')">
                                                        <i class="fi fi-rr-eye"></i> Lihat
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 text-center text-muted">Tidak ada berkas yang diperlukan.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-check mb-4 bg-primary-subtle p-3 rounded border border-primary">
                    <input class="form-check-input ms-1 mt-1" type="checkbox" id="confirmData" style="transform: scale(1.2);">
                    <label class="form-check-label ms-2 fw-semibold text-primary-emphasis" for="confirmData">
                        Saya menyatakan bahwa seluruh data dan berkas yang saya lampirkan adalah benar dan saya bertanggung jawab penuh atas keasliannya.
                    </label>
                </div>
            @endif

        </div>
        <div class="card-footer bg-white p-4 d-flex justify-content-between">
            @if($step > 1)
                <button type="button" class="btn btn-outline-secondary" wire:click="previousStep">
                    <i class="fi fi-rr-angle-left me-1"></i> Sebelumnya
                </button>
            @else
                <div></div> 
            @endif

            @if($step < $totalSteps)
                <button type="button" class="btn btn-primary" onclick="confirmNext()">
                    Selanjutnya <i class="fi fi-rr-angle-right ms-1"></i>
                </button>
            @else
                <button type="button" class="btn btn-success" onclick="confirmSubmit()">
                    <i class="fi fi-rr-paper-plane me-1"></i> Kirim Pendaftaran
                </button>
            @endif
        </div>
    </div>
    @endif

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true" wire:ignore>
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewTitle">Preview Berkas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="pdfViewer" style="height: 70vh; width: 100%;"></div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="pdfDownloadLink" target="_blank" class="btn btn-primary">
                        <i class="fi fi-rr-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('lib/pdfjs-express/webviewer.min.js') }}"></script>
@endassets

@script
<script>
    let map;
    let marker;
    let webViewerInstance = null;

    window.openPdfPreview = function(fileUrl, fileName) {
        const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        document.getElementById('pdfPreviewTitle').textContent = fileName;
        document.getElementById('pdfDownloadLink').href = fileUrl;

        const viewerElement = document.getElementById('pdfViewer');
        viewerElement.innerHTML = '';

        const ext = fileUrl.split('.').pop().toLowerCase().split('?')[0];

        if (ext === 'pdf') {
            WebViewer({
                path: '{{ asset("lib/pdfjs-express") }}',
                initialDoc: fileUrl,
                disabledElements: [
                    'toolsHeader',
                    'viewControlsButton',
                    'leftPanelButton',
                    'searchButton',
                    'menuButton',
                ]
            }, viewerElement).then(instance => {
                webViewerInstance = instance;
                instance.UI.setTheme('light');
            }).catch(err => {
                viewerElement.innerHTML = `
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                        <i class="fi fi-rr-exclamation fs-1 mb-2"></i>
                        <p>Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Klik di sini untuk membuka file</a>.</p>
                    </div>
                `;
            });
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            viewerElement.innerHTML = `
                <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                    <img src="${fileUrl}" alt="${fileName}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
            `;
        } else {
            viewerElement.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                    <i class="fi fi-rr-file fs-1 mb-2"></i>
                    <p>Tidak dapat preview file ini. <a href="${fileUrl}" target="_blank">Download file</a>.</p>
                </div>
            `;
        }
        modal.show();
    }

    Livewire.hook('morph.updated', ({ component, el }) => {
        if (component.snapshot.data.step === 3) {
            const mapEl = document.getElementById('map');
            if (mapEl && !mapEl.classList.contains('leaflet-container')) {
                if (map) {
                    map.off();
                    map.remove();
                    map = null;
                }
                initMap();
            }
        }
    });

    document.addEventListener('livewire:initialized', () => {
        // Init map if starting at step 3 (rare but possible if logic changes)
         if (@this.step === 3) {
             initMap();
         }
    });

    function initMap() {
        const lat = @this.latitude || -6.8168; // Default Cianjur
        const lng = @this.longitude || 107.135;

        map = L.map('map').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lng], {draggable: true}).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            @this.set('latitude', position.lat);
            @this.set('longitude', position.lng);
        });

        // Trigger resize to fix gray map issue
        setTimeout(() => map.invalidateSize(), 500);
    }

    window.getLocation = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    @this.set('latitude', lat);
                    @this.set('longitude', lng);
                    
                    if (map && marker) {
                        const newLatLng = new L.LatLng(lat, lng);
                        marker.setLatLng(newLatLng);
                        map.setView(newLatLng, 15);
                    }
                },
                (error) => {
                     Swal.fire('Error', 'Gagal mendapatkan lokasi: ' + error.message, 'error');
                }
            );
        } else {
             Swal.fire('Error', 'Browser Anda tidak mendukung Geolocation.', 'error');
        }
    }
    window.confirmSubmit = function() {
        Swal.fire({
            title: 'Konfirmasi Pendaftaran',
            text: "Apakah Anda yakin ingin mengirim data pendaftaran ini? Data yang dikirim tidak dapat diubah.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('submit');
            }
        });
    }

    window.confirmNext = function() {
        Swal.fire({
            title: 'Lanjut ke Langkah Berikutnya?',
            text: "Data pada langkah ini akan disimpan sementara.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjut',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('nextStep');
            }
        });
    }
</script>
@endscript
