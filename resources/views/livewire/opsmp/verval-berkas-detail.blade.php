<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1">Verifikasi Berkas</h4>
            <p class="text-muted mb-0">
                {{ $pendaftaran->pesertaDidik->nama ?? '-' }}
                <span class="badge bg-secondary">{{ $pendaftaran->pesertaDidik->nisn ?? '-' }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('opsmp.pendaftaran') }}" class="btn btn-secondary">
                <i class="fi fi-rr-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Student Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0"><i class="fi fi-rr-user me-2"></i>Data Peserta Didik</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted d-block">Nama Lengkap</small>
                    <strong>{{ $pendaftaran->pesertaDidik->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">NISN</small>
                    <strong>{{ $pendaftaran->pesertaDidik->nisn ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Jenis Kelamin</small>
                    <strong>{{ $pendaftaran->pesertaDidik->jenis_kelamin ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Tempat, Tgl Lahir</small>
                    <strong>{{ $pendaftaran->pesertaDidik->tempat_lahir ?? '-' }},
                        {{ $pendaftaran->pesertaDidik->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->pesertaDidik->tanggal_lahir)->locale('id')->isoFormat('D MMMM Y') : '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Sekolah Asal</small>
                    <strong>{{ $pendaftaran->pesertaDidik->sekolah->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Alamat</small>
                    <strong>{{ $pendaftaran->pesertaDidik->alamat_jalan ?? '-' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h6 class="mb-0"><i class="fi fi-rr-document me-2"></i>Data Pendaftaran</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">No. Pendaftaran</small>
                    <strong>{{ $pendaftaran->nomor_pendaftaran ?? '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal Daftar (Dibuat)</small>
                    <strong>{{ $pendaftaran->created_at ? $pendaftaran->created_at->locale('id')->isoFormat('D MMMM Y HH:mm') : '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Tanggal Submit</small>
                    <strong>{{ $pendaftaran->submitted_at ? \Carbon\Carbon::parse($pendaftaran->submitted_at)->locale('id')->isoFormat('D MMMM Y HH:mm') : '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Jalur</small>
                    <strong>{{ $pendaftaran->jalur->nama ?? '-' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status</small>
                    @php
                        $statusColors = [
                            'draft' => 'secondary',
                            'submitted' => 'primary',
                            'verified' => 'info',
                            'accepted' => 'success',
                            'rejected' => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$pendaftaran->status] ?? 'secondary' }}">
                        {{ ucfirst($pendaftaran->status) }}
                    </span>
                </div>
                @if($pendaftaran->jarak_meter)
                    <div class="col-md-3">
                        <small class="text-muted d-block">Jarak ke Sekolah</small>
                        <strong>{{ number_format($pendaftaran->jarak_meter / 1000, 2) }} km</strong>
                    </div>
                @endif
                @if($pendaftaran->koordinat)
                    <div class="col-md-3">
                        <small class="text-muted d-block">Koordinat</small>
                        <strong class="small">{{ $pendaftaran->koordinat }}</strong>
                    </div>
                @endif
                @if($pendaftaran->verified_at)
                    <div class="col-md-3">
                        <small class="text-muted d-block">Tanggal Terverifikasi</small>
                        <strong
                            class="text-success">{{ \Carbon\Carbon::parse($pendaftaran->verified_at)->locale('id')->isoFormat('D MMMM Y HH:mm') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Berkas</h5>
        @if($pendaftaran->status != 'draft')
            <button class="btn btn-success btn-sm" wire:click="approveAll" wire:loading.attr="disabled">
                <i class="fi fi-rr-check-double me-1" wire:loading.remove></i>
                <span wire:loading.remove>Setujui Semua</span>
                <span wire:loading>Memproses...</span>
            </button>
        @else
            <button class="btn btn-secondary btn-sm" disabled title="Pendaftaran masih draft">
                <i class="fi fi-rr-lock me-1"></i> Menunggu Finalisasi
            </button>
        @endif
    </div>

    @if($pendaftaran->status == 'draft')
        <div class="alert alert-warning mb-3">
            <i class="fi fi-rr-exclamation-triangle me-2"></i>
            Pendaftaran ini masih berstatus <strong>Draft</strong>. Anda tidak dapat melakukan verifikasi berkas sampai
            siswa mengirimkan pendaftaran (Finalisasi).
        </div>
    @endif

    <!-- Berkas List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Berkas</th>
                            <th>Nama File</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berkasList as $item)
                            <tr>
                                <td>
                                    <i class="fi fi-rr-file me-2"></i>
                                    {{ $item->berkas->nama ?? 'Berkas' }}
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $item->nama_file_asli ?? '-' }}</small>
                                    @if(!empty($item->form_data) && !empty($item->berkas->form_fields))
                                        @php
                                            $formData = $item->form_data;
                                            $fieldDefs = collect($item->berkas->form_fields);
                                            $groupedDefs = $fieldDefs->groupBy('group');
                                            $accordionId = 'accordion-' . $item->id;
                                        @endphp

                                        <div class="accordion accordion-flush mt-2" id="{{ $accordionId }}">
                                            {{-- Ungrouped Fields --}}
                                            @if($groupedDefs->has('') && $groupedDefs['']->count() > 0)
                                                @php $hasUngroupedData = $groupedDefs['']->some(fn($def) => isset($formData[$def['name']])); @endphp
                                                @if($hasUngroupedData)
                                                    <div class="accordion-item border-0">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button collapsed py-2 px-2 bg-light" type="button" 
                                                                data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}-general" 
                                                                style="font-size: 0.8rem;">
                                                                <i class="fi fi-rr-list me-1"></i> Data Umum
                                                            </button>
                                                        </h2>
                                                        <div id="{{ $accordionId }}-general" class="accordion-collapse collapse" data-bs-parent="#{{ $accordionId }}">
                                                            <div class="accordion-body py-2 px-2">
                                                                @foreach($groupedDefs[''] as $def)
                                                                    @php $key = $def['name']; @endphp
                                                                    @if(isset($formData[$key]))
                                                                        <div class="d-flex justify-content-between align-items-center small border-bottom py-1">
                                                                            <span class="text-muted">{{ $def['label'] }}</span>
                                                                            <span class="fw-bold text-dark">{{ $formData[$key] }}</span>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            {{-- Grouped Fields --}}
                                            @foreach($groupedDefs as $groupName => $defs)
                                                @if(!empty($groupName))
                                                    @php 
                                                        $hasGroupData = $defs->some(fn($def) => isset($formData[$def['name']]));
                                                        $groupSlug = Str::slug($groupName);
                                                    @endphp
                                                    @if($hasGroupData)
                                                        <div class="accordion-item border-0">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed py-2 px-2 bg-light" type="button" 
                                                                    data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}-{{ $groupSlug }}" 
                                                                    style="font-size: 0.8rem;">
                                                                    <i class="fi fi-rr-folder me-1"></i> {{ $groupName }}
                                                                </button>
                                                            </h2>
                                                            <div id="{{ $accordionId }}-{{ $groupSlug }}" class="accordion-collapse collapse" data-bs-parent="#{{ $accordionId }}">
                                                                <div class="accordion-body py-2 px-2">
                                                                    @foreach($defs as $def)
                                                                        @php $key = $def['name']; @endphp
                                                                        @if(isset($formData[$key]))
                                                                            <div class="d-flex justify-content-between align-items-center small border-bottom py-1">
                                                                                <span class="text-muted">{{ $def['label'] }}</span>
                                                                                <span class="fw-bold text-dark">{{ $formData[$key] }}</span>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $berkasStatusColors = [
                                            'pending' => 'secondary',
                                            'approved' => 'success',
                                            'revision' => 'warning',
                                            'rejected' => 'danger',
                                        ];
                                        $berkasStatusLabels = [
                                            'pending' => 'Pending',
                                            'approved' => 'Disetujui',
                                            'revision' => 'Perbaikan',
                                            'rejected' => 'Ditolak',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $berkasStatusColors[$item->status_berkas] ?? 'secondary' }}">
                                        {{ $berkasStatusLabels[$item->status_berkas] ?? 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->catatan_verifikasi)
                                        <small class="text-muted">{{ Str::limit($item->catatan_verifikasi, 50) }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @php
                                        // Prepare student data for KK verification
                                        $studentInfo = [
                                            'NIK' => $pendaftaran->pesertaDidik->nik ?? '-',
                                            'No. KK' => $pendaftaran->pesertaDidik->no_kk ?? '-',
                                            'Nama Siswa' => $pendaftaran->pesertaDidik->nama ?? '-',
                                            'Tempat Lahir' => $pendaftaran->pesertaDidik->tempat_lahir ?? '-',
                                            'Tanggal Lahir' => $pendaftaran->pesertaDidik->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->pesertaDidik->tanggal_lahir)->format('d/m/Y') : '-',
                                            'Nama Ibu Kandung' => $pendaftaran->pesertaDidik->nama_ibu_kandung ?? '-',
                                            'Nama Ayah' => $pendaftaran->pesertaDidik->nama_ayah ?? '-',
                                            'Alamat' => $pendaftaran->pesertaDidik->alamat_jalan ?? '-',
                                            'Kecamatan' => $pendaftaran->pesertaDidik->kecamatan ?? '-',
                                            'Desa/Kelurahan' => $pendaftaran->pesertaDidik->desa_kelurahan ?? '-',
                                        ];
                                    @endphp
                                    <button type="button" class="btn btn-sm btn-subtle-primary waves-effect waves-light"
                                        onclick="openPdfPreview({{ json_encode(asset('storage/' . $item->file_path)) }}, {{ json_encode($item->berkas->nama ?? 'Berkas') }}, {{ json_encode($item->form_data) }}, {{ json_encode($item->berkas->form_fields ?? []) }}, {{ json_encode($studentInfo) }})">
                                        <i class="fi fi-rr-eye me-1"></i> Lihat
                                    </button>
                                    @if($pendaftaran->status != 'draft')
                                        @if($item->status_berkas !== 'approved')
                                            <button class="btn btn-sm btn-subtle-success waves-effect waves-light"
                                                wire:click="quickApprove({{ $item->id }})" wire:loading.attr="disabled"
                                                wire:target="quickApprove({{ $item->id }})">
                                                <i class="fi fi-rr-check me-1" wire:loading.remove></i>
                                                <span wire:loading.remove>Setujui</span>
                                                <span wire:loading><span class="spinner-border spinner-border-sm"></span></span>
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-subtle-secondary waves-effect waves-light"
                                            wire:click="openModal({{ $item->id }})">
                                            <i class="fi fi-rr-edit me-1"></i> Ubah
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-subtle-secondary waves-effect waves-light" disabled>
                                            <i class="fi fi-rr-lock me-1"></i> Draft
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada berkas yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    @if($showModal && $selectedBerkas)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Status Berkas</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted">Jenis Berkas</label>
                            <div class="fw-bold">{{ $selectedBerkas->berkas->nama ?? '-' }}</div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary w-100"
                                onclick="openPdfPreview('{{ asset('storage/' . $selectedBerkas->file_path) }}', '{{ $selectedBerkas->berkas->nama ?? 'Berkas' }}', {{ json_encode($selectedBerkas->form_data) }}, {{ json_encode($selectedBerkas->berkas->form_fields ?? []) }})">
                                <i class="fi fi-rr-eye me-2"></i> Lihat Berkas
                            </button>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Status Verifikasi</label>
                            <select class="form-select" wire:model="newStatus">
                                <option value="pending">Pending</option>
                                <option value="approved">Disetujui</option>
                                <option value="revision">Perbaikan</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            @error('newStatus') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Wajib untuk Perbaikan/Ditolak)</label>
                            <textarea class="form-control" rows="3" wire:model="catatan"
                                placeholder="Masukkan alasan atau catatan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="updateStatus">
                            <i class="fi fi-rr-check me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- PDF Preview Modal -->
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfPreviewTitle">preview Berkas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0 h-100">
                        <!-- Metadata Side -->
                        <div class="col-md-3 border-end bg-light p-3 d-none" id="metaDataPanel"
                            style="height: 70vh; overflow-y: auto;">
                            {{-- Student Data Card (for KK verification) --}}
                            <div id="studentDataSection" class="d-none">
                                <h6 class="fw-bold mb-2 border-bottom pb-2 text-success">
                                    <i class="fi fi-rr-user me-2"></i>Data Siswa (Referensi)
                                </h6>
                                <div id="studentDataContent" class="mb-3"></div>
                            </div>

                            <div id="dataInputSection">
                                <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i
                                        class="fi fi-rr-database me-2"></i>Data Input</h6>
                                <div id="metaDataContent"></div>
                            </div>
                        </div>
                        <!-- Viewer Side -->
                        <div class="col-md-12" id="viewerPanel" style="height: 70vh;">
                            <div id="pdfViewerContainer" style="width: 100%; height: 100%;" wire:ignore></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="pdfDownloadLink" target="_blank" class="btn btn-primary">
                        <i class="fi fi-rr-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('lib/pdfjs-express/webviewer.min.js') }}"></script>
    <script>
        let webViewerInstance = null;

        function openPdfPreview(fileUrl, fileName, formData = null, formFields = [], studentData = null) {
            const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
            document.getElementById('pdfPreviewTitle').textContent = fileName;
            document.getElementById('pdfDownloadLink').href = fileUrl;

            // --- Handle Form Data Sidebar ---
            const metaPanel = document.getElementById('metaDataPanel');
            const viewerPanel = document.getElementById('viewerPanel');
            const metaContent = document.getElementById('metaDataContent');
            const studentSection = document.getElementById('studentDataSection');
            const studentContent = document.getElementById('studentDataContent');
            const dataInputSection = document.getElementById('dataInputSection');

            // Check if should show sidebar (has form data or is KK-related document)
            const isKKDocument = fileName.toLowerCase().includes('kartu keluarga') || fileName.toLowerCase().includes('kk');
            const hasFormData = formData && Object.keys(formData).length > 0;
            const showSidebar = hasFormData || isKKDocument;

            if (showSidebar) {
                // Show Sidebar
                metaPanel.classList.remove('d-none');
                viewerPanel.classList.remove('col-md-12');
                viewerPanel.classList.add('col-md-9');

                // For KK documents: hide Data Input, show Student Data only
                if (isKKDocument) {
                    dataInputSection.classList.add('d-none');
                } else {
                    dataInputSection.classList.remove('d-none');
                }

                let html = '';

                // Group fields by their group property
                if (formFields && Array.isArray(formFields) && formFields.length > 0) {
                    // Build grouped structure
                    const grouped = {};
                    const ungrouped = [];

                    formFields.forEach(field => {
                        const groupName = field.group || '';
                        const fieldData = {
                            name: field.name,
                            label: field.label,
                            value: formData[field.name] || ''
                        };

                        if (groupName) {
                            if (!grouped[groupName]) {
                                grouped[groupName] = [];
                            }
                            grouped[groupName].push(fieldData);
                        } else {
                            ungrouped.push(fieldData);
                        }
                    });

                    // Render ungrouped fields first
                    if (ungrouped.length > 0) {
                        ungrouped.forEach(field => {
                            if (field.value) {
                                html += `
                                                                <div class="card card-body p-2 mb-2 border shadow-none bg-white">
                                                                    <small class="text-muted d-block" style="font-size: 0.75rem;">${field.label}</small>
                                                                    <span class="fw-bold text-dark text-break">${field.value}</span>
                                                                </div>
                                                            `;
                            }
                        });
                    }

                    // Render grouped fields
                    for (const [groupName, fields] of Object.entries(grouped)) {
                        const hasData = fields.some(f => f.value);
                        if (hasData) {
                            html += `
                                                            <div class="card border shadow-none mb-3">
                                                                <div class="card-header bg-primary text-white py-2 px-3" style="font-size: 0.85rem;">
                                                                    <i class="fi fi-rr-folder me-1"></i>${groupName}
                                                                </div>
                                                                <div class="card-body p-2 bg-white">
                                                        `;
                            fields.forEach(field => {
                                if (field.value) {
                                    html += `
                                                                    <div class="d-flex justify-content-between align-items-start border-bottom py-1 px-1" style="font-size: 0.8rem;">
                                                                        <span class="text-muted">${field.label}</span>
                                                                        <span class="fw-bold text-dark text-end ms-2" style="max-width: 60%;">${field.value}</span>
                                                                    </div>
                                                                `;
                                }
                            });
                            html += `
                                                                </div>
                                                            </div>
                                                        `;
                        }
                    }
                } else {
                    // Fallback: render without grouping
                    for (const [key, value] of Object.entries(formData)) {
                        if (value) {
                            html += `
                                                            <div class="card card-body p-2 mb-2 border shadow-none bg-white">
                                                                <small class="text-muted d-block" style="font-size: 0.75rem;">${key}</small>
                                                                <span class="fw-bold text-dark text-break">${value}</span>
                                                            </div>
                                                        `;
                        }
                    }
                }

                metaContent.innerHTML = html;

                // Handle Student Data Section (ONLY for KK verification)
                if (isKKDocument && studentData && Object.keys(studentData).length > 0) {
                    studentSection.classList.remove('d-none');
                    let studentHtml = '';
                    for (const [label, value] of Object.entries(studentData)) {
                        if (value && value !== '-') {
                            studentHtml += `
                                                <div class="d-flex justify-content-between align-items-start border-bottom py-1" style="font-size: 0.8rem;">
                                                    <span class="text-muted">${label}</span>
                                                    <span class="fw-bold text-dark text-end ms-2">${value}</span>
                                                </div>
                                            `;
                        }
                    }
                    studentContent.innerHTML = studentHtml;
                } else {
                    studentSection.classList.add('d-none');
                    studentContent.innerHTML = '';
                }
            } else {
                // Hide Sidebar (Full Width Viewer)
                metaPanel.classList.add('d-none');
                viewerPanel.classList.remove('col-md-9');
                viewerPanel.classList.add('col-md-12');
                metaContent.innerHTML = '';
                studentSection.classList.add('d-none');
                studentContent.innerHTML = '';
            }
            // -------------------------------

            const viewerElement = document.getElementById('pdfViewerContainer');
            if (!viewerElement) return;
            viewerElement.innerHTML = ''; // Clear container

            // Check file extension
            // Remove params if any
            const cleanUrl = fileUrl.split('?')[0];
            const ext = cleanUrl.split('.').pop().toLowerCase();

            if (ext === 'pdf') {
                // STATELSS: Always start fresh with a new element
                const newViewerDiv = document.createElement('div');
                newViewerDiv.style.width = '100%';
                newViewerDiv.style.height = '100%';
                viewerElement.appendChild(newViewerDiv);

                webViewerInstance = null; // Clear old reference

                WebViewer({
                    path: '{{ asset("lib/pdfjs-express") }}',
                    initialDoc: fileUrl,
                    disabledElements: [
                        'notesPanel',
                        'notesPanelButton',
                        'toggleNotesButton',
                        'toolsHeader',
                        'searchButton',
                        'menuButton',
                        'rubbers',
                        'highlightToolGroupButton',
                        'underlineToolGroupButton',
                        'strikeoutToolGroupButton',
                        'squigglyToolGroupButton',
                        'stickyToolGroupButton',
                        'freeHandToolGroupButton',
                        'freeTextToolGroupButton',
                        'freeHandHighlightToolGroupButton',
                        'shapeToolGroupButton',
                    ]
                }, newViewerDiv).then(instance => {
                    webViewerInstance = instance;
                    instance.UI.setTheme('light');
                }).catch(err => {
                    console.error(err);
                    viewerElement.innerHTML = `
                                                                                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                                                                                <i class="fi fi-rr-exclamation fs-1 mb-2"></i>
                                                                                                                <p>Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Klik di sini untuk membuka file</a>.</p>
                                                                                                            </div>
                                                                                                        `;
                });
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                // Clear viewer for image
                viewerElement.innerHTML = '';
                if (webViewerInstance) {
                    webViewerInstance = null;
                }

                // Display image directly
                viewerElement.innerHTML = `
                                                                                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                                                                            <img src="${fileUrl}" alt="${fileName}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                                                                        </div>
                                                                                                    `;
            } else {
                // Clear viewer for unsupported
                viewerElement.innerHTML = '';
                if (webViewerInstance) {
                    webViewerInstance = null;
                }

                // Unsupported file type
                viewerElement.innerHTML = `
                                                                                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                                                                            <i class="fi fi-rr-file fs-1 mb-2"></i>
                                                                                                            <p>Preview tidak tersedia untuk jenis file ini.</p>
                                                                                                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary">
                                                                                                                <i class="fi fi-rr-download me-1"></i> Download File
                                                                                                            </a>
                                                                                                        </div>
                                                                                                    `;
            }

            modal.show();
        }

        // Clean up when modal is closed
        document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function () {
            const ctr = document.getElementById('pdfViewerContainer');
            if (ctr) ctr.innerHTML = '';
            webViewerInstance = null;
        });
    </script>
@endpush