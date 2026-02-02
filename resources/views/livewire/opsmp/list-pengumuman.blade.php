<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Pengumuman Hasil Seleksi</h4>
            <p class="text-muted mb-0">Data siswa yang telah diproses status kelulusannya.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-danger waves-effect waves-light" onclick="confirmResetData()">
                <i class="fi fi-rr-trash me-1"></i> Reset Data
            </button>
        </div>
    </div>

    <script>
        function confirmResetData() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua data pengumuman akan dihapus permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset Data!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resetData');
                }
            })
        }
    </script>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-start justify-content-between border-0">
                    <div>
                        <h2 class="fw-bold mb-0">{{ $statistics['total'] ?? 0 }}</h2>
                        <span class="text-muted">Total Pengumuman</span>
                    </div>
                    <span class="badge bg-primary-subtle text-primary p-2">
                        <i class="fi fi-rr-bullhorn fs-4"></i>
                    </span>
                </div>
                <div class="card-body pt-0">
                    <div class="text-muted small">Total siswa yang telah diproses.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-start justify-content-between border-0">
                    <div>
                        <h2 class="fw-bold mb-0">{{ $statistics['lulus'] ?? 0 }}</h2>
                        <span class="text-muted">Lulus Seleksi</span>
                    </div>
                    <span class="badge bg-success-subtle text-success p-2">
                        <i class="fi fi-rr-check fs-4"></i>
                    </span>
                </div>
                <div class="card-body pt-0">
                    <div class="text-muted small">Siswa diterima disekolah ini.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-start justify-content-between border-0">
                    <div>
                        <h2 class="fw-bold mb-0">{{ $statistics['tidak_lulus'] ?? 0 }}</h2>
                        <span class="text-muted">Tidak Lulus</span>
                    </div>
                    <span class="badge bg-danger-subtle text-danger p-2">
                        <i class="fi fi-rr-cross fs-4"></i>
                    </span>
                </div>
                <div class="card-body pt-0">
                    <div class="text-muted small">Siswa tidak diterima disekolah ini.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between border-0 pb-0">
            <h6 class="card-title mb-0">Data Pengumuman</h6>
            <div class="d-flex gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fi fi-rr-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Nama / NISN..."
                        wire:model.live="search">
                </div>
                <select class="form-select form-select-sm w-auto bg-light border-0" wire:model.live="filterJalur">
                    <option value="">Semua Jalur</option>
                    @foreach($jalurList as $jalur)
                        <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm w-auto bg-light border-0" wire:model.live="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="lulus">Lulus</option>
                    <option value="tidak_lulus">Tidak Lulus</option>
                </select>
                <div class="d-flex gap-1" style="max-width: 300px;">
                    <input type="date" class="form-control form-control-sm bg-light border-0"
                        wire:model.live="dateStart" placeholder="Dari">
                    <input type="date" class="form-control form-control-sm bg-light border-0" wire:model.live="dateEnd"
                        placeholder="Sampai">
                </div>
                <button class="btn btn-sm btn-light btn-icon text-danger" wire:click="resetFilter"
                    data-bs-toggle="tooltip" title="Reset Filter">
                    <i class="fi fi-rr-refresh"></i>
                </button>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Jalur Pendaftaran</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Tanggal Proses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengumumans as $p)
                            <tr>
                                <td>{{ $loop->iteration + $pengumumans->firstItem() - 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $p->pesertaDidik->nama }}</div>
                                    <small class="text-muted">{{ $p->pesertaDidik->nisn }}</small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $p->jalur->nama }}</span>
                                </td>
                                <td>
                                    @if($p->status == 'lulus')
                                        <span class="badge bg-success">
                                            <i class="fi fi-rr-check me-1"></i> LULUS
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fi fi-rr-cross-small me-1"></i> TIDAK LULUS
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $p->keterangan }}</td>
                                <td>{{ $p->created_at->isoFormat('D MMMM Y HH:mm') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fi fi-rr-search-alt fs-1 text-muted opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">Tidak ada data ditemukan</h6>
                                    <p class="text-muted mb-0">Coba ubah filter pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $pengumumans->links() }}
            </div>
        </div>
    </div>


</div>