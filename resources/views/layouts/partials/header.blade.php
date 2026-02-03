@php
    $currentUser = auth()->user();
    if (!$currentUser && auth('siswa')->check()) {
        $currentUser = auth('siswa')->user();
    }
@endphp

<header class="app-header">
    <div class="app-header-inner">
        <button class="app-toggler" type="button" aria-label="app toggler">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="app-header-start d-none d-md-flex">
            <span class="text-muted">
                <i class="fi fi-rr-calendar me-1"></i>
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
        <div class="app-header-end">
            @if(auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                <a href="{{ route('admin.eligible-siswa-domisili') }}"
                    class="btn btn-icon {{ request()->routeIs('admin.eligible-siswa-domisili') ? 'btn-primary text-white' : 'btn-action-gray' }} rounded-circle waves-effect waves-light me-2"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Eligible Siswa Domisili">
                    <i class="fi fi-rr-map-marker-home scale-1x"></i>
                </a>
            @endif

            {{-- Notification Bell for Siswa --}}
            @if(auth('siswa')->check())
                <div class="me-2">
                    @livewire('components.notification-bell')
                </div>
            @endif

            <div class="px-lg-3 px-2 ps-0 d-flex align-items-center">
                <div class="dropdown">
                    <button
                        class="btn btn-icon btn-action-gray rounded-circle waves-effect waves-light position-relative"
                        id="ld-theme" type="button" data-bs-auto-close="outside" aria-expanded="false"
                        data-bs-toggle="dropdown">
                        <i class="fi fi-rr-brightness scale-1x theme-icon-active"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="light">
                                <i class="fi fi-rr-brightness scale-1x"></i> Light
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="dark">
                                <i class="fi fi-rr-moon scale-1x"></i> Dark
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="auto">
                                <i class="fi fi-br-circle-half-stroke scale-1x"></i> Auto
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="vr my-3"></div>
            @if($currentUser)
                <div class="dropdown text-end ms-sm-3 ms-2 ms-lg-4">
                    <a href="#" class="d-flex align-items-center py-2" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">
                        <div class="text-end me-2 d-none d-lg-inline-block">
                            <div class="fw-bold text-dark">{{ $currentUser->name }}</div>
                            <small class="text-body d-block lh-sm">
                                <i class="fi fi-rr-angle-down text-3xs me-1"></i> {{ $currentUser->role_label }}
                            </small>
                        </div>
                        <div class="avatar avatar-sm rounded-circle avatar-status-success bg-primary text-white">
                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end w-225px mt-1">
                        <li class="d-flex align-items-center p-2">
                            <div class="avatar avatar-sm rounded-circle bg-primary text-white">
                                {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                            </div>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">{{ $currentUser->name }}</div>
                                <small class="text-body d-block lh-sm">{{ $currentUser->username }}</small>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1"></div>
                        </li>
                        @if(method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings') }}">
                                    <i class="fi fi-rr-settings scale-1x"></i> Pengaturan
                                </a>
                            </li>
                            <li>
                                <div class="dropdown-divider my-1"></div>
                            </li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <i class="fi fi-sr-exit scale-1x"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</header>