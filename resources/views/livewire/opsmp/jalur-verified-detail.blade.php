<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Siswa Terverifikasi</h4>
            <p class="text-muted mb-0">
                Jalur Pendaftaran: <span class="fw-bold text-primary">{{ $jalur->nama }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('opsmp.jalur-verified') }}" class="btn btn-secondary">
                <i class="fi fi-rr-arrow-left me-1"></i> Kembali
            </a>
        </div>
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
                                    @if(str_contains(strtolower($jalur->nama), 'zonasi') || str_contains(strtolower($jalur->nama), 'domisili'))
                                        @if($pendaftaran->jarak_meter)
                                            <div class="fw-bold">{{ number_format($pendaftaran->jarak_meter / 1000, 2) }} km</div>
                                            <small class="text-muted">{{ number_format($pendaftaran->jarak_meter, 0) }} m</small>
                                        @else
                                            -
                                        @endif
                                    @elseif(str_contains(strtolower($jalur->nama), 'ranking') || str_contains(strtolower($jalur->nama), 'prestasi'))
                                        @if(isset($pendaftaran->ranking_score) && $pendaftaran->ranking_score > 0)
                                            <div class="fw-bold text-primary">Score: {{ $pendaftaran->ranking_score }}</div>
                                            @if(!empty($pendaftaran->ranking_details))
                                                <small class="text-muted d-block" style="font-size: 10px;">
                                                    Ranks: {{ implode(', ', $pendaftaran->ranking_details) }}
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