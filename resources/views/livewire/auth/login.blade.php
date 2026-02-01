<div class="auth-cover-wrapper">
    <div class="row g-0">
        <div class="col-lg-6">
            <div class="auth-cover"
                style="background-image: url({{ asset('templates/assets/images/auth/auth-cover-bg.png') }});">
                <div class="clearfix">
                    <img src="{{ asset('templates/assets/images/auth/auth.png') }}" alt=""
                        class="img-fluid cover-img ms-5">
                    <div class="auth-content">
                        <h1 class="display-6 fw-bold">Selamat Datang!</h1>
                        <p>Sistem Penerimaan Murid Baru SMP Disdikpora Kabupaten Cianjur. Login untuk mengakses
                            dashboard Anda.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 align-self-center">
            <div class="p-3 p-sm-5 maxw-450px m-auto auth-inner" data-simplebar>
                <div class="mb-4 text-center">
                    <a href="{{ url('/') }}" aria-label="Logo"
                        class="d-inline-flex align-items-center justify-content-center text-decoration-none gap-2">
                        @if(function_exists('get_setting') && get_setting('app_logo_image'))
                            <img src="{{ asset('storage/' . get_setting('app_logo_image')) }}" alt="Logo"
                                style="height: 48px;">
                        @else
                            <img src="{{ asset('templates/assets/images/logo.svg') }}" alt="Logo" style="height: 48px;">
                        @endif

                        @if(function_exists('get_setting') && get_setting('app_logo_text_image'))
                            <img src="{{ asset('storage/' . get_setting('app_logo_text_image')) }}" alt="Logo Text"
                                style="height: 32px;">
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
                <div class="text-center mb-5">
                    <h5 class="mb-1">
                        {{ function_exists('get_setting') ? get_setting('app_name', 'SPMB Disdikpora Cianjur') : 'SPMB Disdikpora Cianjur' }}
                    </h5>
                    <p>Masukkan username dan password untuk login.</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form wire:submit="login">
                    <div class="mb-4">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                            wire:model="username" placeholder="Masukkan username" autofocus>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" wire:model="password" placeholder="********">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="remember" wire:model="remember">
                                <label class="form-check-label" for="remember">Ingat Saya</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Login</span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1" role="status"
                                    aria-hidden="true"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <p class="mb-0">Siswa dari luar wilayah / belum terdaftar? <br>
                        <a href="{{ route('register-mandiri') }}" class="fw-bold text-primary">Daftar Akun Mandiri</a>
                    </p>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} Disdikpora Kabupaten Cianjur
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>