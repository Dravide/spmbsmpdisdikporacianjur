<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Under Construction | {{ get_setting('app_name', 'SPMB Cianjur') }}</title>

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

        <div class="maintenance-full-wrapper"
            style="background-image: url({{ asset('templates/assets/images/background/bg-full.png') }});">
            <div class="row g-xl-7 g-5 align-items-center">
                <div class="col-md-5">
                    <img src="{{ asset('templates/assets/images/maintenance/vector2.png') }}" alt="Maintenance Vector"
                        class="img-fluid">
                </div>
                <div class="col-md-7">
                    <div class="maintenance-wrapper">
                        <div class="maintenance-status">Under
                            <br>Construction
                        </div>
                        <h2 class="maintenance-heading text-primary mb-3">Halaman Ini Sedang Dalam Perbaikan.</h2>
                        <p class="maintenance-text mb-4 maxw-md-500px">
                            Website kami sedang dalam pemeliharaan untuk meningkatkan layanan.
                            Kami akan segera kembali! Terima kasih telah mengunjungi kami.
                        </p>

                        {{-- Optional Subscribe or Home Button --}}
                        <a href="{{ url('/') }}" class="btn btn-primary rounded-pill waves-effect waves-light">
                            <i class="fi fi-rr-refresh me-2"></i> Refresh Halaman
                        </a>

                        @if(function_exists('get_setting') && get_setting('app_logo_image'))
                            <div class="mt-4">
                                <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo"
                                    style="max-height: 40px; opacity: 0.8;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('templates/assets/libs/global/global.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/main.js') }}"></script>
</body>

</html>