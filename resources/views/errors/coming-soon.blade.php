<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coming Soon | {{ get_setting('app_name', 'SPMB Cianjur') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('templates/assets/images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/flaticon/css/all/all.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/lucide/lucide.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/simplebar/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/node-waves/waves.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/bootstrap-select/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/css/styles.css') }}">
</head>

<body>
    <div class="page-layout">

        <div class="coming-cover-wrapper">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="coming-wrapper">
                        <div class="maintenance-wrapper mb-5">
                            <div class="mb-4">
                                <a href="{{ url('/') }}" aria-label="Logo"
                                    class="d-inline-flex align-items-center justify-content-center text-decoration-none gap-2">
                                    @if(function_exists('get_setting') && get_setting('app_logo_image'))
                                        <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo"
                                            style="height: 48px;">
                                    @else
                                        <img src="{{ asset('templates/assets/images/logo.svg') }}" alt="Logo"
                                            style="height: 48px;">
                                    @endif

                                    @if(function_exists('get_setting') && get_setting('app_logo_text_image'))
                                        <img src="{{ asset('storage/' . get_setting('app_logo_text_image')) }}"
                                            alt="Logo Text" style="height: 40px;" class="ms-2">
                                    @else
                                        <span class="visible-light fw-bold fs-3 text-dark">
                                            {{ function_exists('get_setting') ? get_setting('app_logo_text', 'SPMB') : 'SPMB' }}
                                        </span>
                                        <span class="visible-dark fw-bold fs-3 text-white">
                                            {{ function_exists('get_setting') ? get_setting('app_logo_text', 'SPMB') : 'SPMB' }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                            <div class="maintenance-status mb-2">Coming Soon</div>
                            <h2 class="maintenance-heading text-primary mb-3">Kami Segera Hadir. Terima Kasih atas
                                Kesabaran Anda.</h2>
                            <p class="maintenance-text mb-4 maxw-md-550px">
                                Kami sedang mempersiapkan sesuatu yang luar biasa untuk Sistem Penerimaan Murid Baru.
                                Pantau terus website ini untuk informasi pendaftaran.
                            </p>
                        </div>

                        <div id="countdown" class="countdown">
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

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="coming-cover"
                        style="background-image: url({{ asset('templates/assets/images/background/coming-soon.png') }});">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('templates/assets/libs/global/global.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/coming-soon.js') }}"></script>
    <script src="{{ asset('templates/assets/js/main.js') }}"></script>
</body>

</html>