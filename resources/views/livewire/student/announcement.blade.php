<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <h3 class="fw-bold">Pengumuman Hasil Seleksi PPDB</h3>
                <p class="text-muted">Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
            </div>

            @if(!$isScheduleOpen)
                <div class="card border-0 shadow-sm text-center card-body p-5">
                    <div class="mb-4">
                        <i class="fi fi-rr-calendar-clock text-secondary" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">PENGUMUMAN BELUM DIBUKA</h4>
                    <p class="text-muted fs-5">
                        {{ $scheduleMessage }}
                    </p>

                    @if($scheduleStartDate)
                        <div class="mb-4" x-data="countdown('{{ $scheduleStartDate }}')">
                            <p class="text-xs font-weight-bold text-uppercase text-muted mb-2">Akan dibuka dalam:</p>
                            <div class="d-flex justify-content-center gap-3">
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary" x-text="days">00</h3>
                                    <small class="text-muted text-xs">Hari</small>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary">:</h3>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary" x-text="hours">00</h3>
                                    <small class="text-muted text-xs">Jam</small>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary">:</h3>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary" x-text="minutes">00</h3>
                                    <small class="text-muted text-xs">Menit</small>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary">:</h3>
                                </div>
                                <div class="text-center">
                                    <h3 class="fw-bold mb-0 text-primary" x-text="seconds">00</h3>
                                    <small class="text-muted text-xs">Detik</small>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('alpine:init', () => {
                                Alpine.data('countdown', (targetDate) => ({
                                    target: new Date(targetDate).getTime(),
                                    now: new Date().getTime(),
                                    days: '00',
                                    hours: '00',
                                    minutes: '00',
                                    seconds: '00',
                                    interval: null,
                                    init() {
                                        this.update();
                                        this.interval = setInterval(() => {
                                            this.now = new Date().getTime();
                                            this.update();
                                        }, 1000);
                                    },
                                    update() {
                                        const distance = this.target - this.now;

                                        if (distance < 0) {
                                            clearInterval(this.interval);
                                            window.location.reload();
                                            return;
                                        }

                                        this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                                        this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                                        this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                                        this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                                    }
                                }))
                            })
                        </script>
                    @endif
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary mt-3">
                        <i class="fi fi-rr-home me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            @elseif($pengumuman)
                @if($pengumuman->status == 'lulus')
                    <!-- Lulus -->
                    <div class="card border-0 shadow-sm text-center card-body p-5">
                        <div class="mb-4">
                            <i class="fi fi-rr-confetti text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="fw-bold text-success mb-3">SELAMAT!</h2>
                        <p class="fs-5 mb-4">Anda dinyatakan <span class="fw-bold text-success">LULUS</span> seleksi di:</p>

                        <div class="alert alert-success d-inline-block px-5 py-3 mb-4">
                            <h4 class="fw-bold mb-0 text-uppercase">{{ $pengumuman->sekolah->nama }}</h4>
                            <div class="mt-2">{{ $pengumuman->jalur->nama }}</div>
                        </div>

                        <p class="text-muted mb-4">
                            Silakan melakukan daftar ulang sesuai dengan jadwal yang telah ditentukan.<br>
                            Bawa bukti pendaftaran dan dokumen asli saat melakukan daftar ulang.
                        </p>

                        <div class="d-flex justify-content-center gap-2">
                            @if($pengumuman->pendaftaran_id)
                                <a href="{{ route('print.bukti-lulus', $pengumuman->pendaftaran_id) }}" target="_blank"
                                    class="btn btn-success">
                                    <i class="fi fi-rr-print me-1"></i> Cetak Bukti Lulus
                                </a>
                                <a href="{{ route('print.bukti', $pengumuman->pendaftaran_id) }}" target="_blank"
                                    class="btn btn-outline-success">
                                    <i class="fi fi-rr-print me-1"></i> Cetak Bukti Pendaftaran
                                </a>
                            @endif
                            <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
                                <i class="fi fi-rr-home me-1"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                @elseif($pengumuman->status == 'tidak_lulus')
                    <!-- Tidak Lulus -->
                    <div class="card border-0 shadow-sm text-center card-body p-5">
                        <div class="mb-4">
                            <i class="fi fi-rr-cross-circle text-danger" style="font-size: 5rem;"></i>
                        </div>
                        <h2 class="fw-bold text-danger mb-3">MOHON MAAF</h2>
                        <p class="fs-5 mb-4">Anda dinyatakan <span class="fw-bold text-danger">TIDAK LULUS</span> seleksi di:
                        </p>

                        <div class="bg-light d-inline-block px-5 py-3 mb-4 rounded">
                            <h4 class="fw-bold mb-0 text-uppercase">{{ $pengumuman->sekolah->nama }}</h4>
                            <div class="mt-2">{{ $pengumuman->jalur->nama }}</div>
                        </div>

                        <p class="text-muted">
                            Jangan berkecil hati. Masih ada kesempatan lain di masa depan.<br>
                            Tetap semangat belajar dan berusaha!
                        </p>

                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary mt-3">
                            <i class="fi fi-rr-home me-1"></i> Kembali ke Dashboard
                        </a>
                    </div>
                @else
                    <!-- Menunggu / Lainnya -->
                    <div class="card border-0 shadow-sm text-center card-body p-5">
                        <div class="mb-4">
                            <i class="fi fi-rr-time-fast text-warning" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">HASIL BELEM KELUAR</h4>
                        <p class="text-muted">
                            Pengumuman untuk sekolah <strong>{{ $pengumuman->sekolah->nama }}</strong> belum dimumumkan secara
                            resmi.<br>
                            Silakan cek kembali secara berkala.
                        </p>
                    </div>
                @endif
            @else
                <!-- Belum Ada Data -->
                <div class="card border-0 shadow-sm text-center card-body p-5">
                    <div class="mb-4">
                        <i class="fi fi-rr-info text-secondary" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">BELUM ADA PENGUMUMAN</h4>
                    <p class="text-muted">
                        Anda belum memiliki data pengumuman hasil seleksi.<br>
                        Pastikan Anda sudah melakukan pendaftaran dan data sudah diverifikasi.
                    </p>
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary mt-3">
                        <i class="fi fi-rr-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>