<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manajemen Jadwal SPMB</h5>
                    <button wire:click="create" class="btn btn-primary btn-sm">
                        <i class="fi fi-rr-add me-1"></i> Tambah Jadwal
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Cari jadwal..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Label</th>
                                    <th>Keyword</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($jadwals as $jadwal)
                                    <tr>
                                        <td>{{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}
                                        </td>
                                        <td class="fw-bold">{{ $jadwal->label }}</td>
                                        <td><code>{{ $jadwal->keyword }}</code></td>
                                        <td>
                                            {{ $jadwal->tanggal_mulai->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>
                                            {{ $jadwal->tanggal_selesai->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="flexSwitchCheckChecked{{ $jadwal->id }}"
                                                    wire:click="toggleActive({{ $jadwal->id }})" {{ $jadwal->aktif ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="flexSwitchCheckChecked{{ $jadwal->id }}">
                                                    @if($jadwal->aktif)
                                                        @if(now()->between($jadwal->tanggal_mulai, $jadwal->tanggal_selesai))
                                                            <span class="badge bg-success">Opened</span>
                                                        @elseif(now()->lessThan($jadwal->tanggal_mulai))
                                                            <span class="badge bg-warning">Upcoming</span>
                                                        @else
                                                            <span class="badge bg-secondary">Closed</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-danger">Disabled</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <button wire:click="edit({{ $jadwal->id }})"
                                                class="btn btn-sm btn-info text-white me-1">
                                                <i class="fi fi-rr-edit"></i>
                                            </button>
                                            <button onclick="confirmDelete({{ $jadwal->id }})"
                                                class="btn btn-sm btn-danger">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">Belum ada data jadwal.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $jadwals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="jadwalModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label class="form-label">Label Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror"
                                wire:model.live="label" placeholder="Contoh: Pendaftaran Tahap 1">
                            @error('label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keyword (Unik) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('keyword') is-invalid @enderror"
                                wire:model="keyword" placeholder="Contoh: pendaftaran-1">
                            <div class="form-text">Digunakan untuk pengecekan di sistem kode.</div>
                            @error('keyword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                    wire:model="tanggal_mulai">
                                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local"
                                    class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                    wire:model="tanggal_selesai">
                                @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi / Pesan Penutup</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                wire:model="deskripsi" rows="3"
                                placeholder="Pesan yang muncul jika jadwal ditutup..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="checkAktif" wire:model="aktif">
                            <label class="form-check-label" for="checkAktif">Aktifkan Jadwal</label>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modal = new bootstrap.Modal(document.getElementById('jadwalModal'));

            Livewire.on('open-modal', () => {
                modal.show();
            });

            Livewire.on('close-modal', () => {
                modal.hide();
            });

            Livewire.on('swal:success', (data) => {
                Swal.fire({
                    title: data[0].title,
                    text: data[0].text,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        });

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data jadwal akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.delete(id);
                }
            });
        }
    </script>
@endpush