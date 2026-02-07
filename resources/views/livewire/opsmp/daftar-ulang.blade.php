<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Jadwal Daftar Ulang</h4>
            <p class="text-muted mb-0">Kelola jadwal dan status daftar ulang siswa yang lulus seleksi.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                data-bs-target="#generateModal">
                <i class="fi fi-rr-calendar-clock me-1"></i> Generate Jadwal
            </button>
            <a href="{{ route('opsmp.cetak-daftar-hadir', ['dateStart' => $dateStart, 'dateEnd' => $dateEnd, 'filterStatus' => $filterStatus]) }}"
                target="_blank" class="btn btn-info text-white waves-effect waves-light">
                <i class="fi fi-rr-print me-1"></i> Daftar Hadir
            </a>
            <button class="btn btn-danger waves-effect waves-light" onclick="confirmResetData()">
                <i class="fi fi-rr-trash me-1"></i> Reset Data
            </button>
            <button class="btn btn-secondary waves-effect waves-light" wire:click="openSettingsModal">
                <i class="fi fi-rr-settings me-1"></i> Atur Persyaratan
            </button>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Jadwal Daftar Ulang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Metode Generate</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="generateMode" value="auto"
                                    id="modeAuto">
                                <label class="form-check-label" for="modeAuto">Otomatis (Bagi Rata)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="generateMode"
                                    value="jalur" id="modeJalur">
                                <label class="form-check-label" for="modeJalur">Per Jalur</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" wire:model.live="generateMode" value="sesi"
                                    id="modeSesi">
                                <label class="form-check-label" for="modeSesi">Dalam Sesi</label>
                            </div>
                        </div>
                    </div>

                    @if($generateMode == 'auto')
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('generateDateStart') is-invalid @enderror"
                                wire:model="generateDateStart">
                            @error('generateDateStart') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durasi (Hari)</label>
                            <input type="number" class="form-control @error('generateDays') is-invalid @enderror"
                                wire:model="generateDays" min="1">
                            @error('generateDays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Siswa akan dibagi rata ke dalam jumlah hari ini.</small>
                        </div>
                    @elseif($generateMode == 'jalur')
                        <div class="mb-3">
                            <label class="form-label mb-2">Atur Tanggal Per Jalur</label>
                            @error('generateJalurSettings.*') <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                            <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $jalurList = \App\Models\JalurPendaftaran::all();
                                @endphp
                                @foreach($jalurList as $jalur)
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">{{ $jalur->nama }}</label>
                                        <input type="date" class="form-control form-control-sm"
                                            wire:model="generateJalurSettings.{{ $jalur->id }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($generateMode == 'sesi')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control @error('generateDateStart') is-invalid @enderror"
                                    wire:model="generateDateStart">
                                @error('generateDateStart') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Durasi (Hari)</label>
                                <input type="number" class="form-control @error('generateDays') is-invalid @enderror"
                                    wire:model="generateDays" min="1">
                                @error('generateDays') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Pengaturan Sesi Harian</span>
                                <button type="button" class="btn btn-xs btn-outline-primary" wire:click="addSession">
                                    <i class="fi fi-rr-plus"></i> Tambah Sesi
                                </button>
                            </label>
                            @error('generateSessions') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                            <div class="border rounded p-3 bg-light">
                                @foreach($generateSessions as $index => $session)
                                    <div class="row g-2 mb-2 align-items-end">
                                        <div class="col-5">
                                            <label class="form-label small mb-0">Mulai</label>
                                            <input type="time"
                                                class="form-control form-control-sm @error('generateSessions.' . $index . '.start') is-invalid @enderror"
                                                wire:model="generateSessions.{{ $index }}.start">
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label small mb-0">Selesai</label>
                                            <input type="time"
                                                class="form-control form-control-sm @error('generateSessions.' . $index . '.end') is-invalid @enderror"
                                                wire:model="generateSessions.{{ $index }}.end">
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                                wire:click="removeSession({{ $index }})">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <small class="text-muted d-block mt-2">
                                    <i class="fi fi-rr-info"></i> Total Slot: {{ $generateDays * count($generateSessions) }}
                                    slot
                                    (Hari x Sesi)
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($generateMode != 'sesi')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control" wire:model="generateTimeStart">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control" wire:model="generateTimeEnd">
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Lokasi Daftar Ulang</label>
                        <input type="text" class="form-control" wire:model="generateLocation"
                            placeholder="Contoh: Kampus SMP, Ruang TU">
                    </div>

                    <div class="alert alert-info">
                        <i class="fi fi-rr-info me-1"></i>
                        Semua siswa yang <strong>LULUS</strong> akan dibuatkan jadwal daftar ulang. Jadwal lama (jika
                        ada) akan
                        dihapus.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="generateSchedule"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Generate Sekarang</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between border-0 pb-0">
            <h6 class="card-title mb-0">Data Daftar Ulang</h6>
            <div class="d-flex gap-2 flex-wrap">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fi fi-rr-search"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Nama / NISN..."
                        wire:model.live="search" wire:loading.attr="disabled">
                    <span class="input-group-text bg-light border-start-0" wire:loading>
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </div>
                <select class="form-select form-select-sm w-auto bg-light border-0" wire:model.live="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="sudah">Sudah Daftar Ulang</option>
                    <option value="belum">Belum Daftar Ulang</option>
                </select>
                <div class="d-flex gap-1" style="max-width: 300px;">
                    <input type="date" class="form-control form-control-sm bg-light border-0"
                        wire:model.live="dateStart" placeholder="Dari">
                    <input type="date" class="form-control form-control-sm bg-light border-0" wire:model.live="dateEnd"
                        placeholder="Sampai">
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Urut</th>
                            <th>Nama Siswa</th>
                            <th>Jadwal</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarUlangs as $data)
                            <tr>
                                <td>{{ $loop->iteration + $daftarUlangs->firstItem() - 1 }}</td>
                                <td>
                                    @if($data->nomor_urut)
                                        <span class="badge bg-primary rounded-circle p-2"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                            {{ $data->nomor_urut }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $data->pesertaDidik->nama }}</div>
                                    <small class="text-muted">{{ $data->pesertaDidik->nisn }}</small>
                                    <div class="text-xs text-info">{{ $data->pengumuman->jalur->nama ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($data->tanggal)->isoFormat('D MMMM Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($data->waktu_selesai)->format('H:i') }}
                                    </small>
                                </td>
                                <td>{{ $data->lokasi ?? '-' }}</td>
                                <td>
                                    @if($data->status == 'sudah')
                                        <span class="badge bg-success">
                                            <i class="fi fi-rr-check me-1"></i> SUDAH
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fi fi-rr-clock me-1"></i> BELUM
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light btn-icon" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fi fi-rr-menu-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if($data->status == 'belum')
                                                <li>
                                                    <button class="dropdown-item"
                                                        wire:click="openVerificationModal({{ $data->id }})">
                                                        <i class="fi fi-rr-check text-success me-2"></i> Verifikasi & Tandai
                                                        Sudah
                                                    </button>
                                                </li>
                                            @else
                                                <li>
                                                    <button class="dropdown-item" wire:click="markAsBelum({{ $data->id }})">
                                                        <i class="fi fi-rr-undo text-warning me-2"></i> Tandai Belum
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fi fi-rr-calendar-clock fs-1 text-muted opacity-25"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold">Belum ada jadwal</h6>
                                    <p class="text-muted mb-0">Generate jadwal melalui menu Pengumuman.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $daftarUlangs->links() }}
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeVerificationModal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Verifikasi kelengkapan dokumen untuk siswa: <br><strong>{{ $studentName }}</strong>
                    </p>

                    @if(empty($verificationChecklist))
                        <div class="alert alert-warning small">
                            Belum ada persyaratan dokumen yang diatur oleh Admin.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($verificationChecklist as $label => $isChecked)
                                <label class="list-group-item d-flex gap-2">
                                    <input class="form-check-input flex-shrink-0" type="checkbox"
                                        wire:model="verificationChecklist.{{ $label }}">
                                    <span>
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <div class="alert alert-info mt-3 small mb-0">
                        <i class="fi fi-rr-info me-1"></i>
                        Pastikan semua dokumen fisik telah diterima dan diverifikasi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        wire:click="closeVerificationModal">Batal</button>
                    <button type="button" class="btn btn-success" wire:click="saveVerification"
                        wire:loading.attr="disabled">
                        <i class="fi fi-rr-check me-1"></i> Simpan & Validasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persyaratan Daftar Ulang (Sekolah)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeSettingsModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Daftar Dokumen yang Harus Dibawa Siswa</label>
                        <textarea class="form-control" rows="6" wire:model="syaratDaftarUlang"
                            placeholder="Contoh:&#10;- Fotokopi Kartu Keluarga&#10;- Fotokopi Akta Kelahiran&#10;- Pas Foto 3x4 (2 Lembar)"></textarea>
                        <div class="form-text">Tuliskan satu persyaratan per baris.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                        wire:click="closeSettingsModal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="saveSettings"
                        wire:loading.attr="disabled">
                        <i class="fi fi-rr-disk me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modals = {};
            const verificationModalEl = document.getElementById('verificationModal');
            const settingsModalEl = document.getElementById('settingsModal');

            if (verificationModalEl) modals['verificationModal'] = new bootstrap.Modal(verificationModalEl);
            if (settingsModalEl) modals['settingsModal'] = new bootstrap.Modal(settingsModalEl);

            Livewire.on('open-modal', (event) => {
                let eventId = event.id;
                if (!eventId && event[0]) eventId = event[0].id;

                if (modals[eventId]) {
                    modals[eventId].show();
                }
            });

            Livewire.on('close-modal', (event) => {
                let eventId = event.id;
                if (!eventId && event[0]) eventId = event[0].id;

                if (modals[eventId]) {
                    modals[eventId].hide();
                }
            });
        });

        function confirmResetData() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Semua data jadwal daftar ulang akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resetData');
                }
            })
        }
    </script>
@endpush