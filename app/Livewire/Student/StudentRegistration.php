<?php

namespace App\Livewire\Student;

use App\Models\Berkas;
use App\Models\JalurPendaftaran;
use App\Models\Pendaftaran;
use App\Models\PendaftaranBerkas;
use App\Models\SekolahMenengahPertama;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Pendaftaran Peserta Didik Baru')]
class StudentRegistration extends Component
{
    use WithFileUploads;

    protected $rules = [];

    public function rules()
    {
        $rules = [
            'selectedSekolahId' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'selectedJalurId' => 'nullable',
        ];

        // Dynamic rules for files
        if ($this->step == 5) {
            foreach ($this->requiredBerkas as $berkas) {
                // Skip file validation if file already exists in database
                if (!isset($this->existingBerkas[$berkas->id])) {
                    $rules["berkasFiles.{$berkas->id}"] = $berkas->is_required ? 'required|file|max:2048' : 'nullable|file|max:2048';
                } else {
                    $rules["berkasFiles.{$berkas->id}"] = 'nullable|file|max:2048';
                }

                // Dynamic Form Fields Rules
                if (!empty($berkas->form_fields) && is_array($berkas->form_fields)) {
                    foreach ($berkas->form_fields as $field) {
                        $fieldName = $field['name'] ?? null;
                        if (!$fieldName)
                            continue;

                        $fieldRequired = !empty($field['required']);
                        $existingValue = $this->uploadedBerkasData[$berkas->id][$fieldName] ?? null;

                        if ($fieldRequired && empty($existingValue)) {
                            $rules["uploadedBerkasData.{$berkas->id}.{$fieldName}"] = 'required';
                        } else {
                            $rules["uploadedBerkasData.{$berkas->id}.{$fieldName}"] = 'nullable';
                        }
                    }
                }
            }
        }

        return $rules;
    }

    public $step = 1;
    public $totalSteps = 6;

    // Step 1: Konfirmasi Data Diri
    public $userData;
    public $dataConfirmed = false;

    // Step 2: Pilih Sekolah
    public $searchSekolah = '';
    public $selectedSekolahId = null;
    public $selectedSekolahName = '';

    // Step 2: Koordinat
    public $latitude;
    public $longitude;
    public $distance; // in meters

    // Step 3: Pilih Jalur
    public $selectedJalurId = null;
    public $jalurList = [];

    // Step 4: Upload Berkas
    public $berkasFiles = []; // [berkas_id => file]
    public $requiredBerkas = [];
    public $uploadedBerkasData = []; // [berkas_id => form_data_array]
    public $existingBerkas = []; // [berkas_id => path]

    // Step 5: Validasi (Review Data)
    public $pendaftaranId = null;
    public $isSubmitted = false;
    public $registrationData = null;

    public function mount()
    {
        $this->jalurList = JalurPendaftaran::where('aktif', true)->get();
        // Setup initial coordinates from profile if available
        $this->loadDraft();
    }

    public function loadDraft()
    {
        $user = Auth::user();
        $siswa = ($user instanceof \App\Models\PesertaDidik) ? $user : ($user->pesertaDidik ?? null);

        if (!$siswa)
            return;

        // Load student data for Step 1
        $this->userData = $siswa;
        $this->latitude = $siswa->lintang;
        $this->longitude = $siswa->bujur;

        // Check for existing registration
        $registration = Pendaftaran::where('peserta_didik_id', $siswa->id)
            ->whereIn('status', ['draft', 'submitted', 'verified', 'accepted', 'rejected'])
            ->latest()
            ->first();

        if ($registration) {
            $this->pendaftaranId = $registration->id;
            $this->isSubmitted = $registration->status != 'draft';

            if ($this->isSubmitted) {
                $this->registrationData = $registration;
                // Load uploaded files for display in status
                $uploaded = PendaftaranBerkas::where('pendaftaran_id', $registration->id)->get();
                foreach ($uploaded as $file) {
                    $this->existingBerkas[$file->berkas_id] = $file->file_path;
                    $this->uploadedBerkasData[$file->berkas_id] = $file->form_data ?? [];
                }
                return;
            }

            // Restore Draft Data
            $this->selectedSekolahId = $registration->sekolah_menengah_pertama_id;
            if ($registration->sekolah) {
                $this->selectedSekolahName = $registration->sekolah->nama;
            }

            if ($registration->koordinat_lintang)
                $this->latitude = $registration->koordinat_lintang;
            if ($registration->koordinat_bujur)
                $this->longitude = $registration->koordinat_bujur;
            if ($registration->jarak_meter)
                $this->distance = $registration->jarak_meter;

            $this->selectedJalurId = $registration->jalur_pendaftaran_id;

            // Load required berkas if jalur is selected
            if ($this->selectedJalurId) {
                $this->loadRequiredBerkas($this->selectedJalurId);
            }

            // Load uploaded files
            $uploaded = PendaftaranBerkas::where('pendaftaran_id', $registration->id)->get();
            foreach ($uploaded as $file) {
                $this->existingBerkas[$file->berkas_id] = $file->file_path;
                $this->uploadedBerkasData[$file->berkas_id] = $file->form_data ?? [];
            }

            // Infer Step to Resume
            $this->step = 2;

            if ($this->selectedSekolahId) {
                $this->step = 3;
            }

            if ($this->selectedSekolahId && $this->latitude && $this->longitude) {
                $this->step = 4;
            }

            if ($this->selectedJalurId) {
                $this->step = 5;
            }
        }
    }

    // --- Navigation ---

    public function nextStep()
    {
        $this->validateStep($this->step);
        $this->saveCurrentStep();

        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function saveCurrentStep()
    {
        if ($this->isSubmitted)
            return;

        $user = Auth::user();
        $siswa = ($user instanceof \App\Models\PesertaDidik) ? $user : ($user->pesertaDidik ?? null);

        if (!$siswa)
            return;

        // Data payload
        $data = [
            'peserta_didik_id' => $siswa->id,
            'sekolah_menengah_pertama_id' => $this->selectedSekolahId,
            'jalur_pendaftaran_id' => $this->selectedJalurId,
            'koordinat_lintang' => $this->latitude,
            'koordinat_bujur' => $this->longitude,
            'jarak_meter' => $this->distance,
            'updated_at' => now(),
        ];

        if ($this->pendaftaranId) {
            $pendaftaran = Pendaftaran::find($this->pendaftaranId);
            if ($pendaftaran && $pendaftaran->status == 'draft') {
                $pendaftaran->update($data);
            }
        } else {
            try {
                $data['status'] = 'draft';
                $data['tanggal_daftar'] = now();
                $pendaftaran = Pendaftaran::create($data);
                $this->pendaftaranId = $pendaftaran->id;
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal membuat draft: ' . $e->getMessage());
                return;
            }
        }

        // Handle File Uploads (Step 5)
        if ($this->step == 5 && $this->pendaftaranId) {
            // Processing uploaded files
            foreach ($this->berkasFiles as $berkasId => $file) {
                $path = $file->store('berkas_pendaftaran', 'public');

                PendaftaranBerkas::updateOrCreate(
                    ['pendaftaran_id' => $this->pendaftaranId, 'berkas_id' => $berkasId],
                    [
                        'file_path' => $path,
                        'nama_file_asli' => $file->getClientOriginalName(),
                        'form_data' => $this->uploadedBerkasData[$berkasId] ?? null
                    ]
                );

                $this->existingBerkas[$berkasId] = $path;
            }

            // Save form data only (for existing files or just data updates)
            if (!empty($this->uploadedBerkasData)) {
                foreach ($this->uploadedBerkasData as $berkasId => $formData) {
                    $existing = PendaftaranBerkas::where('pendaftaran_id', $this->pendaftaranId)
                        ->where('berkas_id', $berkasId)
                        ->first();

                    if ($existing) {
                        $existing->form_data = $formData;
                        $existing->save();
                    }
                }
            }
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function setStep($step)
    {
        // Allow jumping back, but validate if jumping forward
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    // --- Steps Logic ---

    public function selectSekolah($id, $name, $lat, $lon)
    {
        $this->selectedSekolahId = $id;
        $this->selectedSekolahName = $name;

        // Auto calculate distance if user location is set
        if ($this->latitude && $this->longitude && $lat && $lon) {
            $this->distance = $this->calculateDistance($this->latitude, $this->longitude, $lat, $lon);
        }
    }

    public function updatedSelectedJalurId($value)
    {
        if ($value) {
            $this->loadRequiredBerkas($value);
        } else {
            $this->requiredBerkas = [];
        }
    }

    public function loadRequiredBerkas($jalurId)
    {
        $jalur = JalurPendaftaran::with('berkas')->find($jalurId);
        if ($jalur) {
            $this->requiredBerkas = $jalur->berkas;
        }
    }

    public function validateStep($step)
    {
        if ($step == 1) {
            // No validation needed for Step 1 anymore, validation is implicitly done by confirming "Next"
        } elseif ($step == 2) {
            $this->validate([
                'selectedSekolahId' => 'required',
            ], ['selectedSekolahId.required' => 'Silakan pilih sekolah tujuan.']);
        } elseif ($step == 3) {
            $this->validate([
                'latitude' => 'required',
                'longitude' => 'required',
            ], [
                'latitude.required' => 'Titik koordinat wajib diisi.',
                'longitude.required' => 'Titik koordinat wajib diisi.',
            ]);
        } elseif ($step == 4) {
            $this->validate([
                'selectedJalurId' => 'required',
            ], ['selectedJalurId.required' => 'Silakan pilih jalur pendaftaran.']);
        } elseif ($step == 5) {
            $rules = [];
            $messages = [];

            foreach ($this->requiredBerkas as $berkas) {
                // Skip file validation if file already exists in database
                if (!isset($this->existingBerkas[$berkas->id])) {
                    if ($berkas->is_required) {
                        $rules["berkasFiles.{$berkas->id}"] = 'required|file|max:2048'; // Max 2MB
                        $messages["berkasFiles.{$berkas->id}.required"] = "Berkas {$berkas->nama} wajib diunggah.";
                    } else {
                        $rules["berkasFiles.{$berkas->id}"] = 'nullable|file|max:2048';
                    }
                }

                // Validate form_data fields
                if (!empty($berkas->form_fields) && is_array($berkas->form_fields)) {
                    foreach ($berkas->form_fields as $field) {
                        $fieldName = $field['name'] ?? null;
                        if (!$fieldName)
                            continue;

                        $fieldRequired = !empty($field['required']);
                        $existingValue = $this->uploadedBerkasData[$berkas->id][$fieldName] ?? null;

                        // Only require field if it's marked required AND doesn't have existing value
                        if ($fieldRequired && empty($existingValue)) {
                            $rules["uploadedBerkasData.{$berkas->id}.{$fieldName}"] = 'required';
                            $messages["uploadedBerkasData.{$berkas->id}.{$fieldName}.required"] =
                                "Field {$field['label']} pada {$berkas->nama} wajib diisi.";
                        }
                    }
                }
            }

            if (!empty($rules)) {
                $this->validate($rules, $messages);
            }
        }
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c);
    }

    public function submit()
    {
        // Debugging
        // session()->flash('error', 'Submit triggered!'); 

        if ($this->isSubmitted)
            return;

        // Validate all steps again
        // Validate all steps again
        try {
            for ($i = 1; $i <= 5; $i++) {
                $this->validateStep($i);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Validasi gagal di langkah ' . $i . ': ' . implode(', ', $e->validator->errors()->all()));
            throw $e;
        }

        // Ensure draft is saved
        if (!$this->pendaftaranId) {
            $this->saveCurrentStep();
        }

        $pendaftaran = null;
        if ($this->pendaftaranId) {
            $pendaftaran = Pendaftaran::find($this->pendaftaranId);
        }

        if (!$pendaftaran) {
            // Record might be deleted or stale ID. Try to recreate.
            $this->pendaftaranId = null;
            $this->saveCurrentStep();

            if ($this->pendaftaranId) {
                $pendaftaran = Pendaftaran::find($this->pendaftaranId);
            }
        }

        if (!$pendaftaran) {
            session()->flash('error', 'Gagal menyimpan data pendaftaran. Silakan coba lagi.');
            return;
        }

        DB::beginTransaction();
        try {
            // Generate Registration Number if not exists
            if (!$pendaftaran->nomor_pendaftaran) {
                $regNumber = 'REG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $pendaftaran->nomor_pendaftaran = $regNumber;
            }

            $pendaftaran->status = 'submitted';
            $pendaftaran->save();

            DB::commit();

            $this->isSubmitted = true;
            $this->registrationData = $pendaftaran->fresh();

            session()->flash('message', 'Pendaftaran berhasil dikirim! Silakan tunggu verifikasi.');
            // return redirect()->route('dashboard.siswa'); // Disabled redirect to show success view

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $sekolahList = [];
        if ($this->step == 2) {
            $sekolahList = SekolahMenengahPertama::query()
                ->when($this->searchSekolah, function ($query) {
                    $query->where('nama', 'like', '%' . $this->searchSekolah . '%');
                })
                ->orderByRaw("FIELD(mode_spmb, 'Full Online', 'Semi Online')")
                ->orderBy('nama')
                ->take(10)
                ->get();
        }

        $existingFiles = [];
        if ($this->pendaftaranId) {
            $files = PendaftaranBerkas::where('pendaftaran_id', $this->pendaftaranId)->get();
            foreach ($files as $file) {
                $existingFiles[$file->berkas_id] = $file;
            }
        }

        return view('livewire.student.student-registration', [
            'sekolahList' => $sekolahList,
            'selectedJalur' => $this->selectedJalurId ? JalurPendaftaran::find($this->selectedJalurId) : null,
            'selectedSekolah' => $this->selectedSekolahId ? SekolahMenengahPertama::find($this->selectedSekolahId) : null,
            'existingFiles' => $existingFiles,
        ]);
    }
}
