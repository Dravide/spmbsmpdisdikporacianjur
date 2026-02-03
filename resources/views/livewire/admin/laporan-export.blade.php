<div>
    {{-- Page Header --}}
    <div class="app-page-head d-flex align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Laporan & Export</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary waves-effect waves-light" wire:click="exportExcel" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="exportExcel">
                <i class="fi fi-rr-download me-1"></i> Download Excel
            </span>
            <span wire:loading wire:target="exportExcel">
                <span class="spinner-border spinner-border-sm me-1"></span> Generating...
            </span>
        </button>
    </div>

    <div class="row">
        {{-- Report Type Cards Row --}}
        <div class="col-12">
            <div class="row g-3 mb-4">
                @foreach($reportTypes as $key => $type)
                    <div class="col-md-4 col-lg-2">
                        <div class="card {{ $reportType === $key ? 'border-primary bg-primary bg-opacity-10' : '' }} h-100" style="cursor: pointer;" wire:click="$set('reportType', '{{ $key }}')">
                            <div class="card-body text-center py-4">
                                <div class="avatar avatar-md {{ $reportType === $key ? 'bg-primary text-white' : 'bg-light text-muted' }} rounded-circle mx-auto mb-3">
                                    <i class="fi {{ $type['icon'] }}"></i>
                                </div>
                                <h6 class="{{ $reportType === $key ? 'text-primary' : '' }} mb-1">{{ $type['name'] }}</h6>
                                <small class="text-muted d-block">{{ Str::limit($type['description'], 40) }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-xxl-8 col-lg-7">
            {{-- Filter Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fi fi-rr-filter me-2 text-primary"></i>Filter Data
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-light btn-shadow waves-effect" wire:click="$set('filterSekolah', ''); $set('filterJalur', ''); $set('filterStatus', '');">
                        <i class="fi fi-rr-refresh me-1"></i>Reset
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Sekolah Tujuan</label>
                            <div wire:ignore>
                                <select class="form-select selectpicker" id="selectSekolah" data-live-search="true" data-size="8" title="Pilih Sekolah">
                                    <option value="">Semua Sekolah</option>
                                    @foreach($sekolahs as $sekolah)
                                        <option value="{{ $sekolah->sekolah_id }}">{{ $sekolah->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small mb-1">Jalur Pendaftaran</label>
                            <select class="form-select" wire:model.live="filterJalur">
                                <option value="">Semua Jalur</option>
                                @foreach($jalurs as $jalur)
                                    <option value="{{ $jalur->id }}">{{ $jalur->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Status</label>
                            <select class="form-select" wire:model.live="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="verified">Terverifikasi</option>
                                <option value="diterima">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Tanggal Mulai</label>
                            <input type="date" class="form-control" wire:model.live="startDate">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small mb-1">Tanggal Akhir</label>
                            <input type="date" class="form-control" wire:model.live="endDate">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preview Table --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fi fi-rr-eye me-2 text-primary"></i>Preview Data
                    </h6>
                    <span class="badge bg-primary rounded-pill">{{ $reportTypes[$reportType]['name'] }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">No. Pendaftaran</th>
                                    <th>Nama Peserta</th>
                                    <th>Sekolah Tujuan</th>
                                    <th>Jalur</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($previewData as $data)
                                    <tr>
                                        <td class="ps-3">
                                            <code class="text-primary">{{ $data->nomor_pendaftaran ?? '-' }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs bg-primary text-white rounded-circle me-2">
                                                    {{ strtoupper(substr($data->pesertaDidik->nama ?? 'N', 0, 1)) }}
                                                </div>
                                                <span>{{ $data->pesertaDidik->nama ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($data->sekolah->nama ?? '-', 30) }}</td>
                                        <td>
                                            <span class="badge badge-sm bg-light text-dark">{{ $data->jalur->nama ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'verified' => 'info',
                                                    'diterima' => 'success',
                                                    'ditolak' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$data->status] ?? 'secondary' }}">
                                                {{ ucfirst($data->status ?? '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fi fi-rr-folder-open fs-1 opacity-50 d-block mb-2"></i>
                                            <p class="mb-0">Tidak ada data yang sesuai filter</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($previewData) > 0)
                    <div class="card-footer border-0 pt-0">
                        <small class="text-muted">Menampilkan 10 data pertama dari hasil filter</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Stats --}}
        <div class="col-xxl-4 col-lg-5">
            {{-- Quick Stats --}}
            <div class="card mb-4">
                <div class="card-header border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fi fi-rr-stats me-2 text-primary"></i>Statistik
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-center">
                                <h3 class="text-primary mb-0">{{ number_format($statistics['total_pendaftar']) }}</h3>
                                <small class="text-muted">Total Pendaftar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-info bg-opacity-10 rounded-3 text-center">
                                <h3 class="text-info mb-0">{{ number_format($statistics['total_peserta_didik']) }}</h3>
                                <small class="text-muted">Peserta Didik</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 text-center">
                                <h3 class="text-success mb-0">{{ number_format($statistics['total_sekolah']) }}</h3>
                                <small class="text-muted">Sekolah SMP</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-center">
                                <h3 class="text-warning mb-0">{{ number_format($statistics['total_jalur']) }}</h3>
                                <small class="text-muted">Jalur</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Breakdown --}}
            <div class="card mb-4">
                <div class="card-header border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fi fi-rr-chart-pie me-2 text-primary"></i>Berdasarkan Status
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $statusLabels = [
                            'pending' => 'Menunggu',
                            'verified' => 'Terverifikasi',
                            'diterima' => 'Diterima',
                            'ditolak' => 'Ditolak',
                        ];
                        $statusColors = [
                            'pending' => 'warning',
                            'verified' => 'info',
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                        ];
                    @endphp
                    @forelse($statistics['by_status'] as $status => $count)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-circle text-{{ $statusColors[$status] ?? 'secondary' }} text-2xs me-2"></i>
                                <span>{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                            </div>
                            <strong>{{ number_format($count) }}</strong>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                    @endforelse
                </div>
            </div>

            {{-- By Jalur --}}
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h6 class="card-title mb-0">
                        <i class="fi fi-rr-road me-2 text-primary"></i>Berdasarkan Jalur
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($statistics['by_jalur'] as $jalur => $count)
                        @php 
                            $maxJalur = count($statistics['by_jalur']) > 0 ? max($statistics['by_jalur']) : 1;
                            $percent = $maxJalur > 0 ? ($count / $maxJalur) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-dark">{{ $jalur }}</span>
                                <strong>{{ number_format($count) }}</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initSelectPicker();
});

document.addEventListener('livewire:navigated', function() {
    initSelectPicker();
});

function initSelectPicker() {
    if (typeof $.fn.selectpicker !== 'undefined') {
        $('#selectSekolah').selectpicker('refresh');
        $('#selectSekolah').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
            @this.set('filterSekolah', $(this).val());
        });
    }
}
</script>
@endpush