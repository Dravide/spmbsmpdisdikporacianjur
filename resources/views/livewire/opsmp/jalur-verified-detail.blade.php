<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Siswa Terverifikasi</h4>
            <p class="text-muted mb-0">
                Jalur Pendaftaran: <span class="fw-bold text-primary">{{ $jalur->nama }}</span>
        </div>
        <div>
            <div class="d-flex gap-2">
                @php
                    $isAkademik = str_contains(strtolower($jalur->nama), 'akademik') || str_contains(strtolower($jalur->nama), 'test') || str_contains(strtolower($jalur->nama), 'ujian') || str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik') || str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran');
                @endphp
                @if($isAkademik && count($verifiedStudents) > 0)
                    <button type="button" onclick="confirmSaveAllNilai()" class="btn btn-success">
                        <i class="fi fi-rr-disk me-1"></i> Simpan Semua Nilai
                    </button>
                @endif
                @if(count($verifiedStudents) > 0)
                    <button type="button" onclick="confirmProcess()" class="btn btn-primary">
                        <i class="fi fi-rr-settings-sliders me-1"></i> Proses Data Pengumuman
                    </button>
                @endif
                <a href="{{ route('opsmp.jalur-verified') }}" class="btn btn-secondary">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmProcess() {
                Swal.fire({
                    title: 'Konfirmasi Proses',
                    text: "Apakah Anda yakin ingin memproses data pengumuman untuk jalur ini? Data yang sudah diproses akan masuk ke halaman Pengumuman.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Proses!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        @this.processAnnouncement();
                    }
                })
            }

            function confirmSaveAllNilai() {
                Swal.fire({
                    title: 'Simpan Semua Nilai?',
                    text: "Semua nilai test yang diinput akan disimpan dan urutan ranking akan diperbarui.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menyimpan...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        saveAllNilaiFromInputs();
                    }
                })
            }

            function saveNilai(pendaftaranId) {
                const input = document.getElementById('nilai-' + pendaftaranId);
                const nilai = input ? input.value : null;

                if (nilai && nilai !== '') {
                    @this.saveNilaiTestDirect(pendaftaranId, parseFloat(nilai));
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nilai Kosong',
                        text: 'Silakan masukkan nilai terlebih dahulu.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }

            function saveAllNilaiFromInputs() {
                const inputs = document.querySelectorAll('.form-control-sm[id^="nilai-"]');
                const nilaiData = {};

                inputs.forEach(input => {
                    const id = input.id.replace('nilai-', '');
                    if (input.value && input.value !== '') {
                        nilaiData[id] = parseFloat(input.value);
                    }
                });

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                @this.saveAllNilaiDirect(nilaiData);
            }

            document.addEventListener('livewire:initialized', () => {
                @this.on('processed', (event) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pengumuman berhasil diproses.',
                        confirmButtonText: 'OK'
                    });
                });

                @this.on('nilaiSaved', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: 'Nilai test berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });

                @this.on('allNilaiSaved', (data) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: `${data.count} nilai test berhasil disimpan. Urutan ranking telah diperbarui.`,
                        confirmButtonText: 'OK'
                    });
                });
            });
        </script>
    @endpush

    <!-- Teknis Kelulusan -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div class="flex-shrink-0">
                <div class="rounded-circle bg-info bg-opacity-10 p-2">
                    <i class="fi fi-rr-info fs-4"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading">Teknis Penentuan Lulus & Tidak Lulus</h5>
                <div class="row g-3">
                    @php
                        $isZonasi = str_contains(strtolower($jalur->nama), 'zonasi') || str_contains(strtolower($jalur->nama), 'domisili') || $jalur->id == 6 || $jalur->id == 7 || $jalur->id == 8;
                        $isRanking = str_contains(strtolower($jalur->nama), 'ranking') || (str_contains(strtolower($jalur->nama), 'prestasi') && !str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik') && !str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran') && !in_array($jalur->id, [6, 7, 8]));
                        $isAkademik = str_contains(strtolower($jalur->nama), 'akademik') || str_contains(strtolower($jalur->nama), 'test') || str_contains(strtolower($jalur->nama), 'ujian') || str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik') || str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran');
                    @endphp

                    @if($isZonasi)
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Jarak terdekat dari sekolah tujuan</li>
                                    <li>Diurutkan dari jarak terkecil ke terbesar</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa terdekat akan dinyatakan lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Jarak lebih jauh dibanding kuota yang tersedia</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($isRanking)
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Score ranking tertinggi (Rank 1-3 per semester)</li>
                                    <li>Sistem poin: Rank 1 = 3 poin, Rank 2 = 2 poin, Rank 3 = 1 poin</li>
                                    <li>Tiebreaker: Total nilai raport tertinggi</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa dengan score tertinggi lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Score ranking lebih rendah dari kuota yang tersedia</li>
                                    <li>Jika score sama, nilai raport lebih rendah</li>
                                </ul>
                            </div>
                        </div>
                    @elseif(str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik'))
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Nilai tertinggi yang diinput pada kolom "Jarak / Nilai Ranking" (0-100)</li>
                                    <li>Tiebreaker 1: Total nilai raport tertinggi</li>
                                    <li>Tiebreaker 2: Waktu verifikasi lebih awal</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa dengan nilai tertinggi lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Nilai lebih rendah dari kuota yang tersedia</li>
                                    <li>Jika nilai sama, verifikasi lebih akhir</li>
                                </ul>
                            </div>
                        </div>
                    @elseif(str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran'))
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Nilai tertinggi yang diinput pada kolom "Jarak / Nilai Ranking" (0-100)</li>
                                    <li>Tiebreaker 1: Total nilai raport tertinggi</li>
                                    <li>Tiebreaker 2: Waktu verifikasi lebih awal</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa dengan nilai tertinggi lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Nilai lebih rendah dari kuota yang tersedia</li>
                                    <li>Jika nilai sama, verifikasi lebih akhir</li>
                                </ul>
                            </div>
                        </div>
                    @elseif($isAkademik)
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Nilai test tertinggi (0-100)</li>
                                    <li>Tiebreaker 1: Total nilai raport tertinggi</li>
                                    <li>Tiebreaker 2: Waktu verifikasi lebih awal</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa dengan nilai tertinggi lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Nilai test lebih rendah dari kuota yang tersedia</li>
                                    <li>Jika nilai sama, nilai raport lebih rendah atau verifikasi lebih akhir</li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-check-circle text-success me-2"></i>Kriteria Utama</h6>
                                <ul class="mb-0 small">
                                    <li>Waktu verifikasi paling awal (First Come First Serve)</li>
                                    <li>Tiebreaker 1: Jarak terdekat dari sekolah tujuan</li>
                                    <li>Tiebreaker 2: Total nilai raport tertinggi</li>
                                    <li>Kuota: {{ $quotaSlot }} siswa terverifikasi pertama lulus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-info ps-3">
                                <h6 class="fw-bold mb-2"><i class="fi fi-rr-cross-circle text-danger me-2"></i>Tidak Lulus</h6>
                                <ul class="mb-0 small">
                                    <li>Siswa di luar {{ $quotaSlot }} urutan teratas</li>
                                    <li>Waktu verifikasi lebih akhir dari kuota yang tersedia</li>
                                    <li>Jika waktu sama, jarak lebih jauh atau nilai raport lebih rendah</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Quota info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="text-white-50 mb-1">Daya Tampung (Kuota)</h5>
                            <h2 class="mb-0 fw-bold">{{ $quotaSlot }} <span class="fs-6 fw-normal">Siswa</span></h2>
                        </div>
                        <div class="text-end">
                            <h5 class="text-white-50 mb-1">Terisi</h5>
                            <h2 class="mb-0 fw-bold">{{ count($verifiedStudents) }} <span
                                    class="fs-6 fw-normal">Siswa</span></h2>
                        </div>
                    </div>
                    <div class="mt-3">
                        @php
                            $count = count($verifiedStudents);
                            $percent = $quotaSlot > 0 ? ($count / $quotaSlot) * 100 : 0;
                            $percent = min(100, $percent);
                        @endphp
                        <div class="progress bg-white bg-opacity-25" style="height: 6px;">
                            <div class="progress-bar bg-white" role="progressbar" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm animate__animated animate__fadeIn">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Ranking</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Siswa</th>
                            <th>Jarak / Nilai Ranking</th>
                            <th>Status Seleksi</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($verifiedStudents as $pendaftaran)
                            @php
                                $isPassing = $loop->iteration <= $quotaSlot;
                            @endphp
                            <tr class="{{ $isPassing ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                <td class="px-4 fw-bold">#{{ $loop->iteration }}</td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark border">{{ $pendaftaran->nomor_pendaftaran }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pendaftaran->pesertaDidik->nama }}</div>
                                    <small class="text-muted">{{ $pendaftaran->pesertaDidik->sekolah->nama ?? '-' }}</small>
                                </td>
                                <td>
                                    @if(str_contains(strtolower($jalur->nama), 'zonasi') || str_contains(strtolower($jalur->nama), 'domisili') || $jalur->id == 6 || $jalur->id == 7 || $jalur->id == 8)
                                        @if($pendaftaran->jarak_meter)
                                            <div class="fw-bold">{{ number_format($pendaftaran->jarak_meter / 1000, 2) }} km</div>
                                            <small class="text-muted">{{ number_format($pendaftaran->jarak_meter, 0) }} m</small>
                                        @else
                                            -
                                        @endif
                                    @elseif(str_contains(strtolower($jalur->nama), 'akademik') || str_contains(strtolower($jalur->nama), 'test') || str_contains(strtolower($jalur->nama), 'ujian') || str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik') || str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran'))
                                        {{-- Akademik/Non-Akademik: Input nilai test --}}
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" class="form-control form-control-sm" style="width: 100px;"
                                                id="nilai-{{ $pendaftaran->id }}" value="{{ $pendaftaran->nilai_test ?? '' }}"
                                                placeholder="Nilai" step="0.01" min="0" max="100"
                                                wire:loading.attr="disabled">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="saveNilai('{{ $pendaftaran->id }}')" title="Simpan Nilai"
                                                wire:loading.attr="disabled">
                                                <i class="fi fi-rr-check" wire:loading.remove></i>
                                                <span wire:loading>
                                                    <span class="spinner-border spinner-border-sm"></span>
                                                </span>
                                            </button>
                                        </div>
                                        @if($pendaftaran->nilai_test)
                                            <small class="text-success mt-1 d-block">
                                                <i class="fi fi-rr-check-circle me-1"></i>Tersimpan:
                                                {{ number_format($pendaftaran->nilai_test, 2) }}
                                            </small>
                                        @endif
                                    @elseif((str_contains(strtolower($jalur->nama), 'ranking') || str_contains(strtolower($jalur->nama), 'prestasi')) && !str_contains(strtolower($jalur->nama), 'prestasi akademik & non-akademik') && !str_contains(strtolower($jalur->nama), 'prestasi tahfidz quran') && !in_array($jalur->id, [6, 7, 8]))
                                        @if(isset($pendaftaran->ranking_score) && $pendaftaran->ranking_score > 0)
                                            <div class="fw-bold text-primary">Score: {{ $pendaftaran->ranking_score }}</div>
                                            @if(!empty($pendaftaran->ranking_details))
                                                <small class="text-muted d-block" style="font-size: 10px;">
                                                    Ranks: {{ implode(', ', $pendaftaran->ranking_details) }}
                                                </small>
                                            @endif
                                            @if(isset($pendaftaran->raport_total) && $pendaftaran->raport_total > 0)
                                                <small class="text-success d-block" style="font-size: 10px;">
                                                    <i class="fi fi-rr-document me-1"></i>Total Nilai:
                                                    {{ number_format($pendaftaran->raport_total, 0, ',', '.') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($isPassing)
                                        <span class="badge bg-success">
                                            <i class="fi fi-rr-check me-1"></i> Masuk Kuota
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fi fi-rr-cross-circle me-1"></i> Di Luar Kuota
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end px-4">
                                    <a href="{{ route('opsmp.verval-berkas-detail', $pendaftaran->id) }}"
                                        class="btn btn-sm btn-outline-primary bg-white">
                                        <i class="fi fi-rr-search-alt me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-box-open fs-1 d-block mb-3 opacity-50"></i>
                                    Belum ada siswa terverifikasi pada jalur ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>