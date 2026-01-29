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

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small text-muted">Nama Lengkap</label>
                                <div class="fw-bold fs-5">{{ $userData->nama ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">NISN</label>
                                <div class="fw-bold fs-5">{{ $userData->nisn ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Sekolah Asal (SD)</label>
                                <div class="fw-bold">{{ $userData->sekolah->nama ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Tempat, Tanggal Lahir</label>
                                <div class="fw-bold">
                                    {{ $userData->tempat_lahir ?? '-' }}, 
                                    {{ $userData->tanggal_lahir ? \Carbon\Carbon::parse($userData->tanggal_lahir)->format('d F Y') : '-' }}
                                </div>
                            </div>
                             <div class="col-12">
                                <label class="small text-muted">Alamat</label>
                                <div class="fw-bold">{{ $userData->alamat_jalan ?? '-' }}</div>
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

                <div id="map" class="mt-3 rounded border" style="height: 400px;" wire:ignore></div>
                <div class="mt-2 text-end">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation()">
                        <i class="fi fi-rr-crosshairs me-1"></i> Gunakan Lokasi Saat Ini
                    </button>
                </div>
            @endif

            <!-- Step 4: Pilih Jalur -->
            @if ($step == 4)
                <h5 class="mb-3">Langkah 4: Pilih Jalur Pendaftaran</h5>
                
                <div class="row g-3">
                    @forelse($jalurList as $jalur)
                        <div class="col-md-6">
                            <label class="card h-100 cursor-pointer {{ $selectedJalurId == $jalur->id ? 'border-primary bg-primary-subtle' : '' }}" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jalur" value="{{ $jalur->id }}" wire:model.live="selectedJalurId">
                                        <label class="form-check-label fw-bold d-block text-dark">
                                            {{ $jalur->nama }}
                                        </label>
                                    </div>
                                    <p class="text-muted small ms-4 mb-2">
                                        {{ $jalur->deskripsi }}
                                    </p>
                                    @if($jalur->kuota)
                                        <span class="badge bg-secondary ms-4">Kuota: {{ $jalur->kuota }}</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @empty
                        <div class="col-12 text-center text-muted">Belum ada jalur pendaftaran yang dibuka.</div>
                    @endforelse
                </div>
                @error('selectedJalurId') <div class="text-danger mt-2">{{ $message }}</div> @enderror
                
                @if($requiredBerkas)
                    <div class="mt-4">
                        <h6>Berkas yang Diperlukan:</h6>
                        <ul class="list-group">
                            @foreach($requiredBerkas as $berkas)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $berkas->nama }}
                                    @if($berkas->is_required)
                                        <span class="badge bg-danger">Wajib</span>
                                    @else
                                        <span class="badge bg-secondary">Opsional</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif

            <!-- Step 5: Upload Berkas -->
            @if ($step == 5)
                <h5 class="mb-3">Langkah 5: Upload Dokumen</h5>
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fi fi-rr-info fs-5 me-2"></i>
                    <small>Pastikan dokumen terbaca jelas. Format: PDF/JPG/PNG. Maks 2MB per file.</small>
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
                <h5 class="mb-3">Langkah 6: Validasi & Kirim</h5>
                
                <div class="card bg-light border mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Nama Peserta Didik</small>
                                <strong>{{ $userData->nama ?? '-' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Sekolah Tujuan</small>
                                <strong>{{ $selectedSekolah->nama ?? '-' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Jalur Pendaftaran</small>
                                <strong>{{ $selectedJalur->nama ?? '-' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Jarak Tempuh</small>
                                <strong>{{ $distance ? number_format($distance / 1000, 2) . ' KM' : '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="confirmData">
                    <label class="form-check-label" for="confirmData">
                        Saya menyatakan bahwa data yang saya isi adalah benar dan dapat dipertanggungjawabkan.
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
        if (component.snapshot.data.step === 3 && !map && document.getElementById('map')) {
            initMap();
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
