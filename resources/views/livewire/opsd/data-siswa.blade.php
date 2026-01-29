<div>
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Data Siswa</h4>
            <p class="text-muted mb-0">Daftar siswa yang terdaftar di sekolah Anda.</p>
        </div>
    </div>

    @if(!$hasSchool)
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="fi fi-rr-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong>Akun Belum Terhubung!</strong>
                <p class="mb-0">Akun Anda belum dihubungkan dengan data sekolah manapun. Hubungi Administrator untuk
                    sinkronisasi.</p>
            </div>
        </div>
    @else
        <!-- Main Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex gap-2">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0">
                                    <i class="fi fi-rr-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Cari Nama, NISN, atau NIK..." wire:model.live.debounce.300ms="search">
                            </div>

                            @if(!empty($selected))
                                <button wire:click="cetakMassal"
                                    class="btn btn-primary d-flex align-items-center flex-shrink-0">
                                    <i class="fi fi-rr-print me-2"></i> Cetak Massal ({{ count($selected) }})
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="text-muted">Total: {{ $pesertaDidikList->total() }} Siswa</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-center" style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                                </th>
                                <th class="border-0">Nama Lengkap</th>
                                <th class="border-0">L/P</th>
                                <th class="border-0">NISN / NIK</th>
                                <th class="border-0">TTL</th>
                                <th class="border-0">Nama Ibu</th>
                                <th class="border-0 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesertaDidikList as $item)
                                <tr wire:key="student-{{ $item->id }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" wire:model.live="selected"
                                            value="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <span class="fw-medium d-block">{{ $item->nama }}</span>
                                        <small class="text-muted">{{ $item->peserta_didik_id }}</small>
                                    </td>
                                    <td>{{ $item->jenis_kelamin }}</td>
                                    <td>
                                        <div class="small">NISN: {{ $item->nisn ?? '-' }}</div>
                                        <div class="small text-muted">NIK: {{ $item->nik ?? '-' }}</div>
                                    </td>
                                    <td>
                                        {{ $item->tempat_lahir }},
                                        {{ $item->tanggal_lahir ? $item->tanggal_lahir->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>
                                        {{ $item->nama_ibu_kandung ?? '-' }}
                                    </td>
                                    <td class="text-end">
                                        <button wire:click="cetakKartu({{ $item->id }})" class="btn btn-sm btn-outline-primary"
                                            title="Cetak Kartu SPMB">
                                            <i class="fi fi-rr-print me-1"></i> Cetak
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        @if($search)
                                            Tidak ada siswa ditemukan untuk "{{ $search }}"
                                        @else
                                            <div class="mb-2">Belum ada data siswa untuk sekolah ini.</div>
                                            <div class="small text-start d-inline-block bg-light p-2 rounded">
                                                <strong>Debug Info:</strong><br>
                                                Sekolah ID Anda: <code>{{ $debugUserSekolahId }}</code>
                                                <hr class="my-1">
                                                @php $sample = \App\Models\PesertaDidik::first(); @endphp
                                                @if($sample)
                                                    Contoh ID Siswa: <code>{{ $sample->sekolah_id }}</code>
                                                @else
                                                    Tabel Siswa Kosong.
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pesertaDidikList->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $pesertaDidikList->links($this->paginationView()) }}
                </div>
            @endif
        </div>
    @endif
</div>