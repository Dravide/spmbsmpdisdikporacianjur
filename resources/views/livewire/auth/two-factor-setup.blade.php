<div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card border-0 shadow-sm" style="max-width: 500px; width: 100%;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="mb-1">Setup Two-Factor Authentication</h4>
                <p class="text-muted">Tingkatkan keamanan akun Anda dengan Google Authenticator.</p>
            </div>

            @if(!$setupComplete)
                <div class="text-center mb-4">
                    <div class="bg-white p-3 d-inline-block rounded mb-3 border">
                        {!! $qrCodeSvg !!}
                    </div>
                    <p class="small text-muted mb-2">Scan QR Code ini menggunakan aplikasi Google Authenticator atau Authy.
                    </p>
                    <div class="d-flex justify-content-center align-items-center">
                        <span class="text-muted small me-2">Secret Key:</span>
                        <code class="fw-bold">{{ $secret }}</code>
                    </div>
                </div>

                <form wire:submit.prevent="confirm">
                    <div class="mb-3">
                        <label class="form-label">Masukkan Kode 6-Digit</label>
                        <input type="text" class="form-control text-center fs-4 letter-spacing-2" wire:model="code"
                            maxlength="6" placeholder="000000" autofocus>
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verifikasi & Aktifkan</button>
                </form>
                <div class="text-center mt-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none btn-sm text-danger">
                            Logout / Batalkan
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fi fi-rr-check-circle text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">2FA Berhasil Diaktifkan!</h5>
                        <p class="text-muted small">Simpan kode pemulihan ini di tempat yang aman. Anda dapat menggunakannya
                            untuk login jika kehilangan akses ke perangkat Anda.</p>
                    </div>

                    <div class="bg-light p-3 rounded text-start mb-4">
                        <div class="row g-2">
                            @foreach($recoveryCodes as $code)
                                <div class="col-6">
                                    <code class="d-block text-dark">{{ $code }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button wire:click="finish" class="btn btn-primary w-100">Lanjutkan ke Dashboard</button>
                </div>
            @endif
        </div>
    </div>
</div>