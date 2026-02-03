<aside class="app-menubar" id="appMenubar">
    <div class="app-navbar-brand">
        <a class="navbar-brand-logo" href="{{ route('dashboard') }}">
            @if(function_exists('get_setting') && get_setting('app_logo_image'))
                <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo" style="max-height: 40px;">
            @else
                <img src="{{ asset('templates/assets/images/logo.svg') }}" alt="Logo">
            @endif
        </a>
        <a class="navbar-brand-mini visible-light" href="{{ route('dashboard') }}">
            @if(function_exists('get_setting') && get_setting('app_logo_text_image'))
                <img src="{{ asset('storage/' . get_setting('app_logo_text_image')) }}" alt="Logo"
                    style="max-height: 30px;">
            @elseif(function_exists('get_setting') && get_setting('app_logo_text'))
                <span class="fw-bold fs-4 text-primary">{{ get_setting('app_logo_text') }}</span>
            @else
                <img src="{{ asset('templates/assets/images/logo-text.svg') }}" alt="Logo">
            @endif
        </a>
        <a class="navbar-brand-mini visible-dark" href="{{ route('dashboard') }}">
            @if(function_exists('get_setting') && get_setting('app_logo_text_image'))
                <img src="{{ asset('storage/' . get_setting('app_logo_text_image')) }}" alt="Logo"
                    style="max-height: 30px;">
            @elseif(function_exists('get_setting') && get_setting('app_logo_text'))
                <span class="fw-bold fs-4 text-white">{{ get_setting('app_logo_text') }}</span>
            @else
                <img src="{{ asset('templates/assets/images/logo-text-white.svg') }}" alt="Logo">
            @endif
        </a>
    </div>
    <nav class="app-navbar" data-simplebar>
        <ul class="menubar">
            @if(!auth('siswa')->check() && !optional(auth()->user())->isOpsmp())
                <li class="menu-heading">
                    <span class="menu-label">Menu Utama</span>
                </li>
                <li class="menu-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                    <a class="menu-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fi fi-rr-apps"></i>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin())
                {{-- Master Data --}}
                <li class="menu-heading">
                    <span class="menu-label">Master Data</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.sekolah') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.sekolah') }}">
                        <i class="fi fi-rr-school"></i>
                        <span class="menu-label">Sekolah Dasar</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.sekolah-smp') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.sekolah-smp') }}">
                        <i class="fi fi-rr-building"></i>
                        <span class="menu-label">Sekolah SMP</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.jalur') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.jalur') }}">
                        <i class="fi fi-rr-road"></i>
                        <span class="menu-label">Jalur Pendaftaran</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.berkas') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.berkas') }}">
                        <i class="fi fi-rr-file"></i>
                        <span class="menu-label">Jenis Berkas</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pemetaan-domisili') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pemetaan-domisili') }}">
                        <i class="fi fi-rr-map-marker"></i>
                        <span class="menu-label">Pemetaan Domisili</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.daya-tampung') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.daya-tampung') }}">
                        <i class="fi fi-rr-chart-pie-alt"></i>
                        <span class="menu-label">Daya Tampung</span>
                    </a>
                </li>

                {{-- Data Pendaftaran --}}
                <li class="menu-heading">
                    <span class="menu-label">Data Pendaftaran</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.peserta-didik') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.peserta-didik') }}">
                        <i class="fi fi-rr-users-alt"></i>
                        <span class="menu-label">Peserta Didik</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pendaftaran') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pendaftaran') }}">
                        <i class="fi fi-rr-clipboard-list"></i>
                        <span class="menu-label">Data Pendaftaran</span>
                    </a>
                </li>

                {{-- Pengaturan --}}
                <li class="menu-heading">
                    <span class="menu-label">Pengaturan & Sistem</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.jadwal') }}">
                        <i class="fi fi-rr-calendar"></i>
                        <span class="menu-label">Jadwal SPMB</span>
                    </a>
                </li>

                {{-- Laporan & Analitik --}}
                <li class="menu-heading">
                    <span class="menu-label">Laporan & Analitik</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.laporan') }}">
                        <i class="fi fi-rr-document"></i>
                        <span class="menu-label">Laporan & Export</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.peta-persebaran') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.peta-persebaran') }}">
                        <i class="fi fi-rr-map"></i>
                        <span class="menu-label">Peta Persebaran</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isOpsd())
                <li class="menu-heading">
                    <span class="menu-label">Operator SD</span>
                </li>
                <li class="menu-item {{ request()->routeIs('opsd.siswa') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsd.siswa') }}">
                        <i class="fi fi-rr-student"></i>
                        <span class="menu-label">Data Siswa</span>
                    </a>
                </li>
            @endif

            @if(auth()->user()->isOpsmp())
                <li class="menu-heading">
                    <span class="menu-label">Main</span>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.dashboard') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.dashboard') }}">
                        <i class="fi fi-rr-apps"></i>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>

                <li class="menu-heading mt-2">
                    <span class="menu-label">Data Master</span>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.daya-tampung') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.daya-tampung') }}">
                        <i class="fi fi-rr-chart-pie-alt"></i>
                        <span class="menu-label">Daya Tampung</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.pemetaan-domisili') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.pemetaan-domisili') }}">
                        <i class="fi fi-rr-map-marker"></i>
                        <span class="menu-label">Pemetaan Domisili</span>
                    </a>
                </li>

                <li class="menu-heading mt-2">
                    <span class="menu-label">Penerimaan</span>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.pendaftaran') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.pendaftaran') }}">
                        <i class="fi fi-rr-clipboard-list"></i>
                        <span class="menu-label">Data Pendaftaran</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.jalur-verified') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.jalur-verified') }}">
                        <i class="fi fi-rr-list-check"></i>
                        <span class="menu-label">Pengolahan Data</span>
                    </a>
                </li>

                <li class="menu-heading mt-2">
                    <span class="menu-label">Pasca Seleksi</span>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.pengumuman') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.pengumuman') }}">
                        <i class="fi fi-rr-megaphone"></i>
                        <span class="menu-label">Data Pengumuman</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('opsmp.daftar-ulang') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('opsmp.daftar-ulang') }}">
                        <i class="fi fi-rr-calendar-clock"></i>
                        <span class="menu-label">Jadwal Daftar Ulang</span>
                    </a>
                </li>
            @endif




            @if(auth('siswa')->check())
                <li class="menu-heading">
                    <span class="menu-label">Menu Siswa</span>
                </li>
                <li class="menu-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('siswa.dashboard') }}">
                        <i class="fi fi-rr-apps"></i>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('siswa.pendaftaran') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('siswa.pendaftaran') }}">
                        <i class="fi fi-rr-form"></i>
                        @php
                            // Check if already registered
                            $isRegistered = \App\Models\Pendaftaran::where('peserta_didik_id', auth('siswa')->id())->exists();
                        @endphp
                        <span class="menu-label">{{ $isRegistered ? 'Status Pendaftaran' : 'Daftar SPMB' }}</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('siswa.pengumuman') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('siswa.pengumuman') }}">
                        <i class="fi fi-rr-megaphone"></i>
                        <span class="menu-label">Pengumuman</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
</aside>