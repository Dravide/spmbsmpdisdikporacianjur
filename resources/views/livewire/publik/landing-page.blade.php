<div class="page-layout">
    <!-- Hero Section using GXON Coming Soon style -->
    <div class="coming-cover-wrapper">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="coming-wrapper">
                    <div class="maintenance-wrapper mb-5">
                        <div class="mb-4">
                            @if(function_exists('get_setting') && get_setting('app_logo_image'))
                                <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo" style="max-height: 60px;">
                            @else
                                <img class="visible-light" src="{{ asset('templates/assets/images/logo-full.svg') }}" alt="Logo">
                                <img class="visible-dark" src="{{ asset('templates/assets/images/logo-full-white.svg') }}" alt="Logo">
                            @endif
                        </div>
                        <div class="maintenance-status mb-2">SPMB {{ date('Y') }}</div>
                        <h2 class="maintenance-heading text-primary mb-3">Sistem Penerimaan Murid Baru SMP Kabupaten Cianjur</h2>
                        <p class="maintenance-text mb-4 maxw-md-550px">
                            Selamat datang di portal SPMB SMP Disdikpora Kabupaten Cianjur. 
                            Daftarkan putra/putri Anda untuk melanjutkan pendidikan ke jenjang Sekolah Menengah Pertama.
                        </p>

                        <div class="d-flex flex-wrap gap-3 mb-4">
                            <a href="{{ route('register-mandiri') }}" class="btn btn-primary btn-lg rounded-pill waves-effect waves-light px-4">
                                <i class="fi fi-rr-user-add me-2"></i>Daftar Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg rounded-pill waves-effect px-4">
                                <i class="fi fi-rr-sign-in-alt me-2"></i>Login
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="row g-3 mt-4">
                            <div class="col-6 col-md-3">
                                <div class="card bg-primary bg-opacity-10 border-0">
                                    <div class="card-body text-center py-3">
                                        <h3 class="text-primary fw-bold mb-0">{{ number_format($statistics['total_pendaftar']) }}</h3>
                                        <small class="text-muted">Pendaftar</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-success bg-opacity-10 border-0">
                                    <div class="card-body text-center py-3">
                                        <h3 class="text-success fw-bold mb-0">{{ number_format($statistics['total_sekolah']) }}</h3>
                                        <small class="text-muted">Sekolah</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-info bg-opacity-10 border-0">
                                    <div class="card-body text-center py-3">
                                        <h3 class="text-info fw-bold mb-0">{{ number_format($statistics['total_jalur']) }}</h3>
                                        <small class="text-muted">Jalur</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card bg-warning bg-opacity-10 border-0">
                                    <div class="card-body text-center py-3">
                                        <h3 class="text-warning fw-bold mb-0">{{ number_format($statistics['total_diterima']) }}</h3>
                                        <small class="text-muted">Diterima</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($countdown)
                        <div id="countdown" class="countdown" data-target="{{ $countdown['date'] }}">
                            <div class="count-item">
                                <span class="time" id="days">00</span>
                                <span class="text">Hari</span>
                            </div>
                            <div class="count-item">
                                <span class="time" id="hours">00</span>
                                <span class="text">Jam</span>
                            </div>
                            <div class="count-item">
                                <span class="time" id="minutes">00</span>
                                <span class="text">Menit</span>
                            </div>
                            <div class="count-item">
                                <span class="time" id="seconds">00</span>
                                <span class="text">Detik</span>
                            </div>
                        </div>
                        <p class="text-muted mt-2 small">{{ $countdown['label'] }}</p>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="coming-cover" style="background-image: url('{{ asset('templates/assets/images/background/coming-soon.png') }}');"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countdownEl = document.getElementById('countdown');
    if (countdownEl && countdownEl.dataset.target) {
        const targetDate = new Date(countdownEl.dataset.target).getTime();

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance > 0) {
                document.getElementById('days').textContent = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                document.getElementById('hours').textContent = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                document.getElementById('minutes').textContent = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                document.getElementById('seconds').textContent = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
            }
        };

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
});
</script>
@endpush