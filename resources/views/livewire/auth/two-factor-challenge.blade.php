<div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card border-0 shadow-sm" style="max-width: 450px; width: 100%;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <h4 class="mb-1">Two-Factor Authentication</h4>
                @if(!$usingRecoveryCode)
                    <p class="text-muted">Masukkan kode autentikasi dari aplikasi Anda.</p>
                @else
                    <p class="text-muted">Masukkan salah satu kode pemulihan darurat Anda.</p>
                @endif
            </div>

            @if(!$usingRecoveryCode)
                <form wire:submit.prevent="verify">
                    <div class="mb-3">
                        <input type="text" class="form-control text-center fs-4 letter-spacing-2" wire:model="code"
                            maxlength="6" placeholder="000000" autofocus>
                        @error('code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Verifikasi</button>
                </form>
                <div class="text-center">
                    <button wire:click="toggleRecovery" class="btn btn-link text-decoration-none btn-sm">
                        Gunakan Kode Pemulihan
                    </button>
                </div>
            @else
                <form wire:submit.prevent="verifyWithRecoveryCode">
                    <div class="mb-3">
                        <input type="text" class="form-control" wire:model="recoveryCode"
                            placeholder="Masukan kode pemulihan">
                        @error('recoveryCode')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">Gunakan Kode Pemulihan</button>
                </form>
                <div class="text-center">
                    <button wire:click="toggleRecovery" class="btn btn-link text-decoration-none btn-sm">
                        Gunakan Kode Autentikasi
                    </button>
                </div>
            @endif


            <div class="border-top pt-3 mt-4 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger text-decoration-none btn-sm">
                        <i class="fi fi-rr-sign-out-alt me-1"></i> Batalkan & Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>