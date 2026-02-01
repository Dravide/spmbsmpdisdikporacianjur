<?php

namespace App\Livewire\Opsd;

use App\Models\PesertaDidik;
use Livewire\Component;

class EditSiswaModal extends Component
{
    public $isOpen = false;
    public $editId;
    public $formData = [];
    public $kecamatanList = [];
    public $desaList = [];

    public $selectedKecamatanCode;
    public $selectedDesaCode;

    protected $listeners = ['editSiswa' => 'openModal'];

    protected $rules = [
        'formData.nama' => 'required|string|max:255',
        'formData.nisn' => 'required|numeric',
        'formData.nik' => 'nullable|numeric',
        'formData.no_kk' => 'nullable|numeric',
        'formData.tempat_lahir' => 'nullable|string',
        'formData.tanggal_lahir' => 'nullable|date',
        'formData.jenis_kelamin' => 'nullable|in:L,P',
        'formData.nama_ibu_kandung' => 'nullable|string',
        'formData.pekerjaan_ibu' => 'nullable|string',
        'formData.penghasilan_ibu' => 'nullable|string',
        'formData.nama_ayah' => 'nullable|string',
        'formData.pekerjaan_ayah' => 'nullable|string',
        'formData.penghasilan_ayah' => 'nullable|string',
        'formData.nama_wali' => 'nullable|string',
        'formData.pekerjaan_wali' => 'nullable|string',
        'formData.penghasilan_wali' => 'nullable|string',
        'formData.kebutuhan_khusus' => 'nullable|string',
        'formData.no_KIP' => 'nullable|string',
        'formData.no_pkh' => 'nullable|string',
        'formData.alamat_jalan' => 'nullable|string',
        'formData.desa_kelurahan' => 'nullable',
        'formData.kecamatan' => 'nullable',
        'formData.rt' => 'nullable|string',
        'formData.rw' => 'nullable|string',
        'formData.nama_dusun' => 'nullable|string',
        'formData.lintang' => 'nullable',
        'formData.bujur' => 'nullable',
    ];

    public function mount()
    {
        // Initial load of kecamatan (optimized)
        $this->loadKecamatan();
    }

    public function openModal($id)
    {
        $this->isOpen = true;
        $this->editId = $id;
        $siswa = PesertaDidik::with('pendaftaran')->findOrFail($id);

        if ($siswa->pendaftaran && in_array($siswa->pendaftaran->status, ['submitted', 'verified', 'accepted'])) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Data tidak dapat diedit karena status pendaftaran: ' . $siswa->pendaftaran->status]);
            $this->closeModal();
            return;
        }

        $this->formData = $siswa->toArray();

        // Format dates
        if ($siswa->tanggal_lahir) {
            $this->formData['tanggal_lahir'] = $siswa->tanggal_lahir->format('Y-m-d');
        }

        // Handle Region Reverse Lookup
        $this->resolvedRegion();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset('formData', 'editId', 'selectedKecamatanCode', 'selectedDesaCode', 'desaList');
        $this->resetErrorBag();
        // Reload kecamatan for next time just in case
        $this->loadKecamatan();
    }

    public function loadKecamatan()
    {
        $this->kecamatanList = \Laravolt\Indonesia\Models\District::where('city_code', '3203')
            ->select('code', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function resolvedRegion()
    {
        $this->selectedKecamatanCode = null;
        $this->selectedDesaCode = null;

        if (!empty($this->formData['kecamatan'])) {
            $kec = \Laravolt\Indonesia\Models\District::where('name', $this->formData['kecamatan'])
                ->where('city_code', '3203')
                ->select('code', 'name') // Optimization
                ->first();

            if ($kec) {
                $this->selectedKecamatanCode = $kec->code;
                $this->loadDesa($kec->code);

                if (!empty($this->formData['desa_kelurahan'])) {
                    $desa = \Laravolt\Indonesia\Models\Village::where('name', $this->formData['desa_kelurahan'])
                        ->where('district_code', $kec->code)
                        ->select('code', 'name') // Optimization
                        ->first();
                    $this->selectedDesaCode = $desa ? $desa->code : null;
                }
            }
        }
    }

    public function updatedSelectedKecamatanCode($value)
    {
        $this->desaList = [];
        $this->selectedDesaCode = null;
        $this->formData['desa_kelurahan'] = '';
        $this->formData['kecamatan'] = '';

        if ($value) {
            $kec = \Laravolt\Indonesia\Models\District::where('code', $value)->select('code', 'name')->first();
            if ($kec) {
                $this->formData['kecamatan'] = $kec->name;
                $this->loadDesa($value);
            }
        }
    }

    public function updatedSelectedDesaCode($value)
    {
        $this->formData['desa_kelurahan'] = '';
        if ($value) {
            $desa = \Laravolt\Indonesia\Models\Village::where('code', $value)->select('code', 'name')->first();
            if ($desa) {
                $this->formData['desa_kelurahan'] = $desa->name;
            }
        }
    }

    public function loadDesa($kecamatanCode)
    {
        $this->desaList = \Laravolt\Indonesia\Models\Village::where('district_code', $kecamatanCode)
            ->select('code', 'name')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function update()
    {
        // Security check
        $siswa = PesertaDidik::with('pendaftaran')->findOrFail($this->editId);
        if ($siswa->pendaftaran && in_array($siswa->pendaftaran->status, ['submitted', 'verified', 'accepted'])) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Data terkunci. Tidak dapat menyimpan perubahan.']);
            return;
        }

        try {
            $this->validate(array_merge($this->rules, [
                'formData.nisn' => 'required|numeric|unique:peserta_didiks,nisn,' . $this->editId,
            ]));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw or handle error
            $detailedErrors = [];
            foreach ($e->validator->errors()->messages() as $key => $messages) {
                foreach ($messages as $msg) {
                    $detailedErrors[] = "$key: $msg";
                }
            }
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Gagal menyimpan: ' . implode(', ', $detailedErrors)]);
            return;
        }

        $siswa = PesertaDidik::findOrFail($this->editId);
        $siswa->update($this->formData);

        $this->closeModal();
        $this->dispatch('siswa-updated'); // Tell parent to refresh
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Data siswa berhasil diperbarui.']);
    }

    public function render()
    {
        return view('livewire.opsd.edit-siswa-modal');
    }
}
