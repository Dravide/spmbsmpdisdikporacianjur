<!DOCTYPE html>
<html lang="id" data-app-sidebar="full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} -
        {{ function_exists('get_setting') ? get_setting('app_name', 'SPMB Disdikpora Cianjur') : 'SPMB Disdikpora Cianjur' }}
    </title>

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
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/libs/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('templates/assets/css/styles.css') }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
    @stack('styles')
</head>

<body>
    <div class="page-layout">
        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="app-wrapper">
            {{ $slot }}
        </div>
    </div>

    <!-- GXON Scripts -->
    <script src="{{ asset('templates/assets/libs/global/global.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('templates/assets/js/appSettings.js') }}"></script>
    <script src="{{ asset('templates/assets/js/main.js') }}"></script>

    @livewireScripts
    @stack('scripts')
</body>

</html>