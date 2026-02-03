<div class="position-relative" x-data="{ open: false }" @click.away="open = false">
    <!-- Bell Button -->
    <button class="btn btn-icon btn-ghost-secondary position-relative" @click="open = !open" type="button">
        <i class="fi fi-rr-bell"></i>
        @if($this->unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                  style="font-size: 10px; padding: 4px 6px;">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" 
         :class="{ 'show': open }"
         style="width: 360px; max-height: 480px;">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
            <h6 class="mb-0">
                <i class="fi fi-rr-bell me-2"></i>Notifikasi
            </h6>
            @if($this->unreadCount > 0)
                <button class="btn btn-sm btn-link text-decoration-none p-0" wire:click="markAllAsRead">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div style="max-height: 350px; overflow-y: auto;">
            @forelse($this->notifications as $notification)
                @php
                    $data = $notification->data;
                    $typeColors = [
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        'info' => 'info',
                    ];
                    $color = $typeColors[$data['type'] ?? 'info'] ?? 'secondary';
                @endphp
                <div class="d-flex p-3 border-bottom position-relative {{ $notification->read_at ? '' : 'bg-light' }}"
                     wire:click="markAsRead('{{ $notification->id }}')" style="cursor: pointer;">
                    @if(!$notification->read_at)
                        <div class="position-absolute start-0 top-0 bottom-0 bg-primary" style="width: 3px;"></div>
                    @endif
                    <div class="me-3">
                        <div class="avatar avatar-sm bg-{{ $color }} bg-opacity-10 text-{{ $color }} rounded-circle">
                            @switch($data['type'] ?? 'info')
                                @case('success')
                                    <i class="fi fi-rr-check"></i>
                                    @break
                                @case('warning')
                                    <i class="fi fi-rr-exclamation"></i>
                                    @break
                                @case('danger')
                                    <i class="fi fi-rr-cross"></i>
                                    @break
                                @default
                                    <i class="fi fi-rr-info"></i>
                            @endswitch
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="{{ $notification->read_at ? 'fw-medium text-body' : 'fw-bold text-primary' }}" style="font-size: 0.9rem;">
                                {{ $data['title'] ?? 'Notifikasi' }}
                            </span>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="{{ $notification->read_at ? 'text-muted' : 'text-body-secondary' }} small mb-0">{{ $data['message'] ?? '' }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fi fi-rr-bell-slash fs-1 text-muted opacity-50"></i>
                    <p class="text-muted mt-2 mb-0">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($this->notifications->count() > 0)
            <div class="p-2 border-top text-center">
                <a href="{{ route('siswa.notifikasi') }}" class="text-decoration-none small">Lihat semua notifikasi</a>
            </div>
        @endif
    </div>
</div>
