<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Peta Persebaran Pendaftar</h4>
            <p class="text-muted mb-0">Visualisasi lokasi domisili pendaftar</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-{{ $mapMode === 'markers' ? 'primary' : 'outline-primary' }}"
                wire:click="$set('mapMode', 'markers')">
                <i class="fi fi-rr-marker me-1"></i>Markers
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-filter me-2 text-primary"></i>Filter</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Sekolah Tujuan</label>
                        <div wire:ignore>
                            <select class="form-select form-select-sm" id="selectSekolahPeta">
                                <option value="">Semua Sekolah</option>
                                @foreach($sekolahs as $sekolah)
                                    <option value="{{ $sekolah->sekolah_id }}" {{ $filterSekolah == $sekolah->sekolah_id ? 'selected' : '' }}>{{ $sekolah->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Jalur</label>
                        <select class="form-select form-select-sm" wire:model.live="filterJalur">
                            <option value="">Semua Jalur</option>
                            @foreach($jalurs as $jalur)
                                <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Status</label>
                        <select class="form-select form-select-sm" wire:model.live="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="verified">Terverifikasi</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Kecamatan</label>
                        <input type="text" class="form-control form-control-sm"
                            wire:model.live.debounce.500ms="filterKecamatan" placeholder="Cari kecamatan...">
                    </div>
                </div>
            </div>

            <!-- Kecamatan Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="mb-0"><i class="fi fi-rr-stats me-2 text-primary"></i>Per Kecamatan</h6>
                </div>
                <div class="card-body p-0">
                    @php $maxKecamatan = count($kecamatanStats) > 0 ? max($kecamatanStats) : 1; @endphp
                    @forelse($kecamatanStats as $kecamatan => $count)
                        <div class="d-flex align-items-center p-2 border-bottom">
                            <div class="flex-grow-1">
                                <div class="small fw-medium">{{ $kecamatan }}</div>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ ($count / $maxKecamatan) * 100 }}%"></div>
                                </div>
                            </div>
                            <span class="badge bg-primary ms-2">{{ $count }}</span>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted small">
                            Belum ada data
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Map Area -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fi fi-rr-map me-2 text-primary"></i>
                        Peta Lokasi
                    </h6>
                    <span class="badge bg-primary" id="markerCount">{{ $totalMarkers }} Pendaftar</span>
                </div>
                <div class="card-body p-0" wire:ignore>
                    <div id="map" style="height: 600px; border-radius: 0 0 12px 12px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }

        .marker-cluster-small {
            background-color: rgba(102, 126, 234, 0.6);
        }

        .marker-cluster-small div {
            background-color: rgba(102, 126, 234, 0.8);
        }

        .marker-cluster-medium {
            background-color: rgba(118, 75, 162, 0.6);
        }

        .marker-cluster-medium div {
            background-color: rgba(118, 75, 162, 0.8);
        }

        .marker-cluster-large {
            background-color: rgba(245, 87, 108, 0.6);
        }

        .marker-cluster-large div {
            background-color: rgba(245, 87, 108, 0.8);
        }

        .marker-cluster {
            color: #fff;
            font-weight: bold;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 31px;
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Select2 for Sekolah dropdown
            $('#selectSekolahPeta').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Sekolah...',
                allowClear: true,
                width: '100%'
            }).on('change', function (e) {
                @this.set('filterSekolah', $(this).val());
            });

            // Initialize map centered on Cianjur
            const map = L.map('map').setView([-6.8213, 107.1409], 10);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            // Create marker cluster group
            const markerCluster = L.markerClusterGroup();
            map.addLayer(markerCluster);

            // Status colors
            const statusColors = {
                'pending': '#ffc107',
                'verified': '#17a2b8',
                'diterima': '#28a745',
                'ditolak': '#dc3545'
            };

            // Load markers function
            const loadMarkers = (data) => {
                markerCluster.clearLayers();

                data.forEach(item => {
                    const color = statusColors[item.status] || '#667eea';

                    const icon = L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="background: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    const marker = L.marker([item.lat, item.lng], { icon: icon });

                    marker.bindPopup(`
                            <div style="min-width: 200px;">
                                <h6 style="margin: 0 0 8px 0; font-weight: 600;">${item.nama}</h6>
                                <table style="font-size: 12px; width: 100%;">
                                    <tr><td style="color: #666;">NISN</td><td style="font-weight: 500;">${item.nisn}</td></tr>
                                    <tr><td style="color: #666;">Sekolah</td><td style="font-weight: 500;">${item.sekolah}</td></tr>
                                    <tr><td style="color: #666;">Jalur</td><td style="font-weight: 500;">${item.jalur}</td></tr>
                                    <tr><td style="color: #666;">Kecamatan</td><td style="font-weight: 500;">${item.kecamatan}</td></tr>
                                    <tr><td style="color: #666;">Status</td><td><span style="background: ${color}; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px;">${item.status}</span></td></tr>
                                </table>
                            </div>
                        `);

                    markerCluster.addLayer(marker);
                });
            };

            // Initial load
            loadMarkers(@json($markers));

            // Listen for Livewire event to update markers
            Livewire.on('markers-updated', (event) => {
                loadMarkers(event.markers);
                document.getElementById('markerCount').textContent = event.total + ' Pendaftar';
            });
        });
    </script>
@endpush