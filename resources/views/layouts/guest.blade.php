<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - SPMB Disdikpora Cianjur</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('templates/assets/images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    <!-- GXON Stylesheets -->
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/flaticon/css/all/all.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/lucide/lucide.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/simplebar/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/node-waves/waves.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/bootstrap-select/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/css/styles.css') }}">

    @livewireStyles
</head>

<body>
    <div class="page-layout">
        {{ $slot }}
    </div>

    <!-- GXON Scripts -->
    <script src="{{ asset('templates/assets/libs/global/global.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/appSettings.js') }}"></script>
    <script src="{{ asset('templates/assets/js/main.js') }}"></script>

    @livewireScripts
</body>

</html>