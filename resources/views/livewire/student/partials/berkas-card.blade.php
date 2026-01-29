@props(['berkas', 'existingFile' => null, 'berkasFiles', 'uploadedBerkasData'])

@php
    $isApproved = $existingFile && $existingFile->status_berkas == 'approved';
    $isRevision = $existingFile && $existingFile->status_berkas == 'revision';
    $isPending = $existingFile && $existingFile->status_berkas == 'pending';
    $isRejected = $existingFile && $existingFile->status_berkas == 'rejected';
@endphp

<div class="card h-100 border-0 shadow-sm {{ $isApproved ? 'bg-success-subtle border-success' : '' }}">
    <div class="card-header bg-white border-bottom fw-medium d-flex justify-content-between align-items-center">
        <span>
            {{ $berkas->nama }}
            @if($berkas->is_required) <span class="text-danger">*</span> @endif
        </span>
        @if($existingFile)
            @if($isApproved)
                <span class="badge bg-success"><i class="fi fi-rr-check-circle me-1"></i>Disetujui</span>
            @elseif($isRevision)
                <span class="badge bg-warning text-dark"><i class="fi fi-rr-exclamation me-1"></i>Perlu Perbaikan</span>
            @elseif($isRejected)
                <span class="badge bg-danger"><i class="fi fi-rr-cross-circle me-1"></i>Ditolak</span>
            @else
                <span class="badge bg-info text-dark"><i class="fi fi-rr-time-fast me-1"></i>Menunggu</span>
            @endif
        @endif
    </div>
    <div class="card-body">
        @if($berkas->deskripsi)
            <p class="small text-muted mb-2">{{ $berkas->deskripsi }}</p>
        @endif
        
        @if($existingFile)
            <div class="alert alert-light border py-2 px-3 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-truncate small" style="max-width: 60%;">
                        <i class="fi fi-rr-file me-1"></i> 
                        <a href="#" onclick="openPdfPreview('{{ Storage::url($existingFile->file_path) }}', '{{ $berkas->nama }}')">Lihat File</a>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="openPdfPreview('{{ Storage::url($existingFile->file_path) }}', '{{ $berkas->nama }}')"
                        title="Preview">
                        <i class="fi fi-rr-eye"></i>
                    </button>
                </div>
            </div>

            @if($isRevision && $existingFile->catatan_verifikasi)
                <div class="alert alert-warning small mb-2">
                    <strong>Catatan:</strong> {{ $existingFile->catatan_verifikasi }}
                </div>
            @endif
        @endif

        @if(!$isApproved)
            <div class="mb-2">
                <label class="form-label small mb-1">
                    {{ $existingFile ? 'Ganti File' : 'Upload File' }}
                </label>
                <input type="file" class="form-control form-control-sm" wire:model="berkasFiles.{{ $berkas->id }}" accept=".pdf,.jpg,.jpeg,.png">
                @error("berkasFiles.{$berkas->id}") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            
            @if(isset($berkasFiles[$berkas->id]))
                <div class="text-success small mb-2">
                    <i class="fi fi-rr-check me-1"></i> File dipilih
                </div>
            @endif
        @else
             <div class="alert alert-success small mb-0">
                <i class="fi fi-rr-lock me-1"></i> File sudah dikunci karena telah disetujui.
             </div>
        @endif

        <!-- Dynamic Form Fields with Grouping -->
        @if(!empty($berkas->form_fields) && is_array($berkas->form_fields))
            <div class="border-top pt-3 mt-2">
                <h6 class="fs-6 mb-3 text-secondary"><i class="fi fi-rr-form me-1"></i> Data Tambahan</h6>
                
                @php
                    $allFields = collect($berkas->form_fields);
                    $groupedFields = $allFields->groupBy('group');
                @endphp

                {{-- 1. Render Ungrouped Fields --}}
                @if($groupedFields->has(''))
                    @foreach($groupedFields[''] as $field)
                        @include('livewire.student.partials.dynamic-field', ['field' => $field, 'berkasId' => $berkas->id, 'index' => $loop->index, 'disabled' => $isApproved])
                    @endforeach
                @endif

                {{-- 2. Render Grouped Fields (Grid Layout) --}}
                @foreach($groupedFields as $groupName => $fields)
                    @if(!empty($groupName))
                        <div class="card mb-3 border bg-light">
                            <div class="card-header py-1 px-3 fw-bold small text-secondary bg-light-subtle">
                                {{ $groupName }}
                            </div>
                            <div class="card-body p-2">
                                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2 align-items-end">
                                    @foreach($fields as $field)
                                         <div class="col">
                                             @include('livewire.student.partials.dynamic-field', [
                                                'field' => $field, 
                                                'berkasId' => $berkas->id, 
                                                'index' => $loop->index,
                                                'compact' => true,
                                                'disabled' => $isApproved
                                             ])
                                         </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
