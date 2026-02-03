<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Log Aktivitas</h4>
            <p class="text-muted mb-0">Riwayat semua aktivitas dalam sistem</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="fi fi-rr-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari deskripsi atau IP..." 
                               wire:model.live.debounce.300ms="search">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterAction">
                        <option value="">Semua Aksi</option>
                        @foreach($actionOptions as $action)
                            <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterType">
                        <option value="">Semua Tipe</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model.live="filterDate">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" wire:click="clearFilters">
                        <i class="fi fi-rr-refresh me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @forelse($logs as $log)
                <div class="d-flex p-3 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                    <!-- Icon -->
                    <div class="me-3">
                        <div class="avatar avatar-sm bg-{{ $log->action_color }} bg-opacity-10 text-{{ $log->action_color }} rounded-circle">
                            @switch($log->action)
                                @case('created')
                                    <i class="fi fi-rr-plus"></i>
                                    @break
                                @case('updated')
                                    <i class="fi fi-rr-pencil"></i>
                                    @break
                                @case('deleted')
                                    <i class="fi fi-rr-trash"></i>
                                    @break
                                @case('login')
                                    <i class="fi fi-rr-sign-in-alt"></i>
                                    @break
                                @case('logout')
                                    <i class="fi fi-rr-sign-out-alt"></i>
                                    @break
                                @case('verified')
                                @case('approved')
                                    <i class="fi fi-rr-check"></i>
                                    @break
                                @case('rejected')
                                    <i class="fi fi-rr-cross"></i>
                                    @break
                                @default
                                    <i class="fi fi-rr-document"></i>
                            @endswitch
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $log->action_color }} me-2">{{ $log->action_label }}</span>
                                <span class="fw-medium">{{ $log->description }}</span>
                            </div>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                        
                        <div class="mt-2 text-muted small">
                            @if($log->causer)
                                <span class="me-3">
                                    <i class="fi fi-rr-user me-1"></i>
                                    {{ $log->causer->name ?? $log->causer->nama ?? 'Unknown' }}
                                </span>
                            @endif
                            @if($log->ip_address)
                                <span class="me-3">
                                    <i class="fi fi-rr-globe me-1"></i>
                                    <code>{{ $log->ip_address }}</code>
                                </span>
                            @endif
                            @if($log->subject_type)
                                <span class="me-3">
                                    <i class="fi fi-rr-document me-1"></i>
                                    {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                </span>
                            @endif
                        </div>

                        @if($log->properties && (isset($log->properties['old']) || isset($log->properties['new'])))
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#changes-{{ $log->id }}">
                                    <i class="fi fi-rr-eye me-1"></i>Lihat Perubahan
                                </button>
                                <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                    <div class="row g-2">
                                        @if(isset($log->properties['old']))
                                            <div class="col-md-6">
                                                <div class="bg-danger bg-opacity-10 rounded p-2">
                                                    <small class="text-danger fw-bold">Sebelum:</small>
                                                    <pre class="mb-0 small">{{ json_encode($log->properties['old'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($log->properties['new']))
                                            <div class="col-md-6">
                                                <div class="bg-success bg-opacity-10 rounded p-2">
                                                    <small class="text-success fw-bold">Sesudah:</small>
                                                    <pre class="mb-0 small">{{ json_encode($log->properties['new'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fi fi-rr-document fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada log aktivitas</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
