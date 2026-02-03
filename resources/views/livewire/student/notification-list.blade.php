<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-flush shadow-sm">
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <div class="card-title">
                        <h2>Notifikasi</h2>
                    </div>
                    <div class="card-toolbar">
                        @if($notifications->count() > 0)
                            <button class="btn btn-sm btn-light-primary" wire:click="markAllAsRead">
                                <i class="fi fi-rr-check-double me-2"></i>Tandai semua dibaca
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex flex-column gap-3">
                        @forelse($notifications as $notification)
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
                            <div class="d-flex align-items-center p-4 rounded-3 border {{ $notification->read_at ? 'bg-body' : 'bg-light border-primary border-start border-3' }}"
                                 style="{{ !$notification->read_at ? 'border-left-width: 4px !important;' : '' }}">
                                
                                <!-- Icon -->
                                <div class="symbol symbol-40px symbol-circle me-4">
                                    <span class="symbol-label bg-light-{{ $color }} text-{{ $color }}">
                                        @switch($data['type'] ?? 'info')
                                            @case('success') <i class="fi fi-rr-check fs-2"></i> @break
                                            @case('warning') <i class="fi fi-rr-exclamation fs-2"></i> @break
                                            @case('danger') <i class="fi fi-rr-cross fs-2"></i> @break
                                            @default <i class="fi fi-rr-info fs-2"></i>
                                        @endswitch
                                    </span>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <a href="{{ $data['action_url'] ?? '#' }}" wire:click="markAsRead('{{ $notification->id }}')" 
                                           class="text-dark fw-bold text-hover-primary fs-6 text-decoration-none">
                                            {{ $data['title'] ?? 'Notifikasi' }}
                                        </a>
                                        <span class="text-muted fs-7">{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-muted fs-7">{{ $data['message'] ?? '' }}</div>
                                </div>

                                <!-- Action -->
                                <div class="ms-4">
                                    @if(!$notification->read_at)
                                        <button class="btn btn-icon btn-sm btn-active-light-primary" wire:click="markAsRead('{{ $notification->id }}')" 
                                                data-bs-toggle="tooltip" title="Tandai dibaca">
                                            <i class="fi fi-rr-envelope fs-5 text-muted"></i>
                                        </button>
                                    @else
                                        <i class="fi fi-rr-envelope-open fs-5 text-muted opacity-50"></i>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="symbol symbol-60px symbol-circle bg-light-secondary mb-4">
                                    <i class="fi fi-rr-bell-slash fs-1 text-muted"></i>
                                </div>
                                <h4 class="text-gray-800 fw-bold mb-0">Tidak ada notifikasi</h4>
                                <p class="text-gray-600 fs-7 mt-1">Anda akan menerima notifikasi jika ada pembaruan status pendaftaran.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
