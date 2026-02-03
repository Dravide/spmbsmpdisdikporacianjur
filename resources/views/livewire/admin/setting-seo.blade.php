<div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Pengaturan SEO</h4>
            </div>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fi fi-rr-check-circle me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'favicon' ? 'active' : '' }}"
                        wire:click="setTab('favicon')" type="button">
                        <i class="fi fi-rr-picture me-2"></i>Favicon
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'opengraph' ? 'active' : '' }}"
                        wire:click="setTab('opengraph')" type="button">
                        <i class="fi fi-rr-share me-2"></i>OpenGraph & Social
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'seo' ? 'active' : '' }}" wire:click="setTab('seo')"
                        type="button">
                        <i class="fi fi-rr-search me-2"></i>SEO Meta
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'google' ? 'active' : '' }}" wire:click="setTab('google')"
                        type="button">
                        <i class="fi fi-rr-chart-line-up me-2"></i>Google Integration
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'advanced' ? 'active' : '' }}"
                        wire:click="setTab('advanced')" type="button">
                        <i class="fi fi-rr-settings-sliders me-2"></i>Advanced
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Favicon Tab -->
                @if($activeTab === 'favicon')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fi fi-rr-picture me-2 text-primary"></i>Pengaturan Favicon</h5>
                            <small class="text-muted">Upload berbagai ukuran favicon untuk kompatibilitas maksimal</small>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit="saveFavicon">
                                <div class="row g-4">
                                    <!-- Favicon 16x16 -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-secondary">16x16</span>
                                                </div>
                                                <div class="border rounded p-3 bg-light mb-3 d-flex align-items-center justify-content-center"
                                                    style="height: 80px;">
                                                    @if ($favicon_16)
                                                        <img src="{{ $favicon_16->temporaryUrl() }}" style="max-height: 60px;">
                                                    @elseif($existing_favicon_16)
                                                        <img src="{{ asset('storage/' . $existing_favicon_16) }}"
                                                            style="max-height: 60px;">
                                                    @else
                                                        <i class="fi fi-rr-picture text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('favicon_16') is-invalid @enderror"
                                                    wire:model="favicon_16" accept="image/png">
                                                @error('favicon_16') <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">Browser Tab Icon</small>
                                                @if($existing_favicon_16)
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        wire:click="deleteFavicon('favicon_16')">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Favicon 32x32 -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-secondary">32x32</span>
                                                </div>
                                                <div class="border rounded p-3 bg-light mb-3 d-flex align-items-center justify-content-center"
                                                    style="height: 80px;">
                                                    @if ($favicon_32)
                                                        <img src="{{ $favicon_32->temporaryUrl() }}" style="max-height: 60px;">
                                                    @elseif($existing_favicon_32)
                                                        <img src="{{ asset('storage/' . $existing_favicon_32) }}"
                                                            style="max-height: 60px;">
                                                    @else
                                                        <i class="fi fi-rr-picture text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('favicon_32') is-invalid @enderror"
                                                    wire:model="favicon_32" accept="image/png">
                                                @error('favicon_32') <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">Taskbar Icon</small>
                                                @if($existing_favicon_32)
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        wire:click="deleteFavicon('favicon_32')">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Apple Touch Icon 180x180 -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-info">180x180</span>
                                                </div>
                                                <div class="border rounded p-3 bg-light mb-3 d-flex align-items-center justify-content-center"
                                                    style="height: 80px;">
                                                    @if ($favicon_180)
                                                        <img src="{{ $favicon_180->temporaryUrl() }}" style="max-height: 60px;">
                                                    @elseif($existing_favicon_180)
                                                        <img src="{{ asset('storage/' . $existing_favicon_180) }}"
                                                            style="max-height: 60px;">
                                                    @else
                                                        <i class="fi fi-rr-mobile-notch text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('favicon_180') is-invalid @enderror"
                                                    wire:model="favicon_180" accept="image/png">
                                                @error('favicon_180') <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">Apple Touch Icon</small>
                                                @if($existing_favicon_180)
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        wire:click="deleteFavicon('favicon_180')">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Android Chrome 192x192 -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-success">192x192</span>
                                                </div>
                                                <div class="border rounded p-3 bg-light mb-3 d-flex align-items-center justify-content-center"
                                                    style="height: 80px;">
                                                    @if ($favicon_192)
                                                        <img src="{{ $favicon_192->temporaryUrl() }}" style="max-height: 60px;">
                                                    @elseif($existing_favicon_192)
                                                        <img src="{{ asset('storage/' . $existing_favicon_192) }}"
                                                            style="max-height: 60px;">
                                                    @else
                                                        <i class="fi fi-rr-mobile-notch text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('favicon_192') is-invalid @enderror"
                                                    wire:model="favicon_192" accept="image/png">
                                                @error('favicon_192') <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">Android Chrome</small>
                                                @if($existing_favicon_192)
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        wire:click="deleteFavicon('favicon_192')">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Android Chrome 512x512 -->
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-warning text-dark">512x512</span>
                                                </div>
                                                <div class="border rounded p-3 bg-light mb-3 d-flex align-items-center justify-content-center"
                                                    style="height: 80px;">
                                                    @if ($favicon_512)
                                                        <img src="{{ $favicon_512->temporaryUrl() }}" style="max-height: 60px;">
                                                    @elseif($existing_favicon_512)
                                                        <img src="{{ asset('storage/' . $existing_favicon_512) }}"
                                                            style="max-height: 60px;">
                                                    @else
                                                        <i class="fi fi-rr-mobile-notch text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                                <input type="file"
                                                    class="form-control form-control-sm @error('favicon_512') is-invalid @enderror"
                                                    wire:model="favicon_512" accept="image/png">
                                                @error('favicon_512') <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-2">PWA Splash Screen</small>
                                                @if($existing_favicon_512)
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                        wire:click="deleteFavicon('favicon_512')">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fi fi-rr-disk me-2"></i>Simpan Favicon
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- OpenGraph Tab -->
                @if($activeTab === 'opengraph')
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="mb-0"><i class="fi fi-rr-share me-2 text-primary"></i>OpenGraph & Social
                                        Media</h5>
                                    <small class="text-muted">Pengaturan tampilan saat link dibagikan di media
                                        sosial</small>
                                </div>
                                <div class="card-body p-4">
                                    <form wire:submit="saveOpenGraph">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">OG Title <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('og_title') is-invalid @enderror"
                                                wire:model.live="og_title" maxlength="100"
                                                placeholder="Judul untuk social media">
                                            @error('og_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <small class="text-muted">{{ strlen($og_title ?? '') }}/100 karakter</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">OG Description <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('og_description') is-invalid @enderror"
                                                wire:model.live="og_description" rows="3" maxlength="300"
                                                placeholder="Deskripsi untuk social media"></textarea>
                                            @error('og_description') <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">{{ strlen($og_description ?? '') }}/300
                                                karakter</small>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label fw-bold">OG Image</label>
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 120px; height: 80px;">
                                                    @if ($og_image)
                                                        <img src="{{ $og_image->temporaryUrl() }}" class="img-fluid"
                                                            style="max-height: 70px;">
                                                    @elseif($existing_og_image)
                                                        <img src="{{ asset('storage/' . $existing_og_image) }}"
                                                            class="img-fluid" style="max-height: 70px;">
                                                    @else
                                                        <i class="fi fi-rr-picture text-muted fs-3"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file"
                                                        class="form-control @error('og_image') is-invalid @enderror"
                                                        wire:model="og_image" accept="image/*">
                                                    @error('og_image') <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="text-muted">Ukuran ideal: 1200x630 pixel</small>
                                                    @if($existing_og_image)
                                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2"
                                                            wire:click="deleteOgImage">
                                                            <i class="fi fi-rr-trash me-1"></i>Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">OG Type</label>
                                                <select class="form-select" wire:model="og_type">
                                                    <option value="website">Website</option>
                                                    <option value="article">Article</option>
                                                    <option value="product">Product</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Locale</label>
                                                <input type="text" class="form-control" wire:model="og_locale"
                                                    placeholder="id_ID">
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <h6 class="fw-bold mb-3"><i class="fi fi-brands-twitter me-2"></i>Twitter Card</h6>

                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Card Type</label>
                                                <select class="form-select" wire:model="twitter_card">
                                                    <option value="summary">Summary</option>
                                                    <option value="summary_large_image">Summary Large Image</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Twitter Username</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">@</span>
                                                    <input type="text" class="form-control" wire:model="twitter_site"
                                                        placeholder="username">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fi fi-rr-disk me-2"></i>Simpan OpenGraph
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <!-- Preview Card -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <h6 class="mb-0"><i class="fi fi-rr-eye me-2"></i>Preview</h6>
                                </div>
                                <div class="card-body p-3">
                                    <!-- Facebook Preview -->
                                    <div class="mb-4">
                                        <small class="text-muted fw-bold d-block mb-2">
                                            <i class="fi fi-brands-facebook me-1"></i>Facebook Preview
                                        </small>
                                        <div class="border rounded overflow-hidden" style="max-width: 500px;">
                                            <div class="bg-light"
                                                style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                                @if ($og_image)
                                                    <img src="{{ $og_image->temporaryUrl() }}" class="w-100 h-100"
                                                        style="object-fit: cover;">
                                                @elseif($existing_og_image)
                                                    <img src="{{ asset('storage/' . $existing_og_image) }}" class="w-100 h-100"
                                                        style="object-fit: cover;">
                                                @else
                                                    <i class="fi fi-rr-picture text-muted fs-1"></i>
                                                @endif
                                            </div>
                                            <div class="p-3 bg-white border-top">
                                                <small class="text-muted text-uppercase"
                                                    style="font-size: 11px;">{{ parse_url($canonical_url ?? config('app.url'), PHP_URL_HOST) }}</small>
                                                <h6 class="mb-1 text-dark" style="font-size: 14px;">
                                                    {{ $og_title ?? 'Judul Halaman' }}</h6>
                                                <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.4;">
                                                    {{ Str::limit($og_description ?? 'Deskripsi halaman akan muncul di sini...', 100) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Twitter Preview -->
                                    <div>
                                        <small class="text-muted fw-bold d-block mb-2">
                                            <i class="fi fi-brands-twitter me-1"></i>Twitter Preview
                                        </small>
                                        <div class="border rounded overflow-hidden" style="max-width: 500px;">
                                            @if($twitter_card === 'summary_large_image')
                                                <div class="bg-light"
                                                    style="height: 130px; display: flex; align-items: center; justify-content: center;">
                                                    @if ($og_image)
                                                        <img src="{{ $og_image->temporaryUrl() }}" class="w-100 h-100"
                                                            style="object-fit: cover;">
                                                    @elseif($existing_og_image)
                                                        <img src="{{ asset('storage/' . $existing_og_image) }}" class="w-100 h-100"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <i class="fi fi-rr-picture text-muted fs-1"></i>
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="p-3 bg-white border-top">
                                                <h6 class="mb-1 text-dark" style="font-size: 14px;">
                                                    {{ $og_title ?? 'Judul Halaman' }}</h6>
                                                <p class="text-muted mb-1" style="font-size: 12px;">
                                                    {{ Str::limit($og_description ?? 'Deskripsi halaman...', 80) }}</p>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <i
                                                        class="fi fi-rr-link me-1"></i>{{ parse_url($canonical_url ?? config('app.url'), PHP_URL_HOST) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- SEO Meta Tab -->
                @if($activeTab === 'seo')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fi fi-rr-search me-2 text-primary"></i>SEO Meta Tags</h5>
                            <small class="text-muted">Pengaturan meta tags untuk optimasi mesin pencari</small>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit="saveSeoMeta">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Meta Description <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                        wire:model.live="meta_description" rows="3" maxlength="160"
                                        placeholder="Deskripsi singkat website Anda untuk hasil pencarian Google"></textarea>
                                    @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Idealnya 150-160 karakter</small>
                                        <small
                                            class="{{ strlen($meta_description ?? '') > 160 ? 'text-danger' : 'text-muted' }}">
                                            {{ strlen($meta_description ?? '') }}/160
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Meta Keywords</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                        wire:model="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                                    @error('meta_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Pisahkan dengan koma. (Catatan: Google tidak lagi menggunakan
                                        meta keywords untuk ranking)</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Canonical URL <span
                                            class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('canonical_url') is-invalid @enderror"
                                        wire:model="canonical_url" placeholder="https://example.com">
                                    @error('canonical_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">URL utama website Anda (tanpa trailing slash)</small>
                                </div>

                                <!-- Google Search Preview -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold"><i class="fi fi-brands-google me-2"></i>Google Search
                                        Preview</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div style="font-family: Arial, sans-serif;">
                                            <div class="text-primary" style="font-size: 18px;">
                                                {{ $og_title ?? get_setting('app_name', 'SPMB Cianjur') }}</div>
                                            <div class="text-success" style="font-size: 14px;">
                                                {{ $canonical_url ?? config('app.url') }}</div>
                                            <div class="text-dark" style="font-size: 13px; color: #545454;">
                                                {{ Str::limit($meta_description ?? '', 160) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fi fi-rr-disk me-2"></i>Simpan SEO Meta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Google Integration Tab -->
                @if($activeTab === 'google')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fi fi-rr-chart-line-up me-2 text-primary"></i>Google Integration</h5>
                            <small class="text-muted">Integrasi dengan layanan Google untuk analytics dan indexing</small>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit="saveGoogleIntegration">
                                <!-- Google Search Console -->
                                <div class="mb-4 p-4 border rounded bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="https://www.gstatic.com/webmasters/resources/icons/search_console-128.png"
                                            alt="Search Console" style="height: 32px;" class="me-3">
                                        <div>
                                            <h6 class="mb-0">Google Search Console</h6>
                                            <small class="text-muted">Verifikasi kepemilikan website di Google</small>
                                        </div>
                                    </div>
                                    <label class="form-label fw-bold">Site Verification Code</label>
                                    <input type="text"
                                        class="form-control @error('google_site_verification') is-invalid @enderror"
                                        wire:model="google_site_verification"
                                        placeholder="Contoh: rXOxyZounnZasA8Z7oaD3c14JdjS9aKSWvsR1EbUSIQ">
                                    @error('google_site_verification') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kode verifikasi dari <a
                                            href="https://search.google.com/search-console" target="_blank">Google Search
                                            Console</a></small>
                                </div>

                                <!-- Google Analytics -->
                                <div class="mb-4 p-4 border rounded bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="https://www.gstatic.com/analytics-suite/header/suite/v2/ic_analytics.svg"
                                            alt="Analytics" style="height: 32px;" class="me-3">
                                        <div>
                                            <h6 class="mb-0">Google Analytics</h6>
                                            <small class="text-muted">Tracking pengunjung dan perilaku pengguna</small>
                                        </div>
                                    </div>
                                    <label class="form-label fw-bold">Google Analytics ID</label>
                                    <input type="text"
                                        class="form-control @error('google_analytics_id') is-invalid @enderror"
                                        wire:model="google_analytics_id"
                                        placeholder="Contoh: G-XXXXXXXXXX atau UA-XXXXXXXX-X">
                                    @error('google_analytics_id') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Measurement ID dari <a href="https://analytics.google.com/"
                                            target="_blank">Google Analytics</a></small>
                                </div>

                                <!-- Google Tag Manager -->
                                <div class="mb-4 p-4 border rounded bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="https://www.gstatic.com/analytics-suite/header/suite/v2/ic_tag_manager.svg"
                                            alt="Tag Manager" style="height: 32px;" class="me-3">
                                        <div>
                                            <h6 class="mb-0">Google Tag Manager</h6>
                                            <small class="text-muted">Kelola semua tracking tags dari satu tempat</small>
                                        </div>
                                    </div>
                                    <label class="form-label fw-bold">Google Tag Manager ID</label>
                                    <input type="text"
                                        class="form-control @error('google_tag_manager_id') is-invalid @enderror"
                                        wire:model="google_tag_manager_id" placeholder="Contoh: GTM-XXXXXXX">
                                    @error('google_tag_manager_id') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Container ID dari <a href="https://tagmanager.google.com/"
                                            target="_blank">Google Tag Manager</a></small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fi fi-rr-info me-2"></i>
                                    <strong>Tips:</strong> Jika Anda menggunakan Google Tag Manager, sebaiknya pasang Google
                                    Analytics melalui GTM untuk pengelolaan yang lebih mudah.
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fi fi-rr-disk me-2"></i>Simpan Google Integration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Advanced Tab -->
                @if($activeTab === 'advanced')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="fi fi-rr-settings-sliders me-2 text-primary"></i>Advanced Settings
                            </h5>
                            <small class="text-muted">Pengaturan lanjutan untuk kontrol SEO penuh</small>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit="saveAdvanced">
                                <!-- Robots.txt Editor -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold mb-0">robots.txt</label>
                                        <a href="{{ url('/robots.txt') }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fi fi-rr-eye me-1"></i>Lihat File
                                        </a>
                                    </div>
                                    <textarea class="form-control font-monospace @error('robots_txt') is-invalid @enderror"
                                        wire:model="robots_txt" rows="8"
                                        style="font-size: 13px; background-color: #1e1e1e; color: #d4d4d4;"></textarea>
                                    @error('robots_txt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Mengontrol bagaimana mesin pencari mengakses halaman website
                                        Anda</small>
                                </div>

                                <!-- Sitemap Toggle -->
                                <div class="mb-4 p-4 border rounded bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Sitemap XML</h6>
                                            <small class="text-muted">Aktifkan auto-generate sitemap.xml</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" wire:model="sitemap_enabled"
                                                style="width: 50px; height: 25px;">
                                        </div>
                                    </div>
                                    @if($sitemap_enabled)
                                        <div class="mt-3">
                                            <a href="{{ url('/sitemap.xml') }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fi fi-rr-link me-1"></i>{{ url('/sitemap.xml') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fi fi-rr-triangle-warning me-2"></i>
                                    <strong>Perhatian:</strong> Perubahan pada robots.txt dapat mempengaruhi indexing
                                    website Anda di mesin pencari. Pastikan Anda memahami apa yang Anda lakukan.
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fi fi-rr-disk me-2"></i>Simpan Advanced Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>