<?php

namespace App\Livewire\Admin;

use App\Models\JalurPendaftaran;
use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Data Jalur Pendaftaran')]
class DataJalur extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Form fields
    public $nama, $deskripsi, $aktif = true, $kuota, $start_date, $end_date;
    public $selectedBerkas = []; // Array of Berkas IDs
    public $jalurId;
    public $isEditMode = false;
    public $showFormModal = false;

    protected $rules = [
        'nama' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'aktif' => 'boolean',
        'kuota' => 'required|numeric|min:0|max:100',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'selectedBerkas' => 'array',
        'selectedBerkas.*' => 'exists:berkas,id',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['nama', 'deskripsi', 'aktif', 'kuota', 'start_date', 'end_date', 'selectedBerkas', 'jalurId', 'isEditMode']);
        $this->aktif = true;
        $this->isEditMode = false;
        $this->showFormModal = true;
    }

    public function edit($id)
    {
        $jalur = JalurPendaftaran::with('berkas')->findOrFail($id);
        $this->jalurId = $jalur->id;
        $this->nama = $jalur->nama;
        $this->deskripsi = $jalur->deskripsi;
        $this->aktif = (bool) $jalur->aktif;
        $this->kuota = $jalur->kuota;
        $this->start_date = $jalur->start_date ? $jalur->start_date->format('Y-m-d') : null;
        $this->end_date = $jalur->end_date ? $jalur->end_date->format('Y-m-d') : null;

        // Load related berkas IDs
        $this->selectedBerkas = $jalur->berkas->pluck('id')->toArray();

        $this->isEditMode = true;
        $this->showFormModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'aktif' => $this->aktif,
            'kuota' => $this->kuota,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        // Validation for Total Percentage (Max 100%)
        if ($this->aktif) {
            $existingQuota = JalurPendaftaran::where('aktif', true)
                ->when($this->isEditMode, function ($q) {
                    $q->where('id', '!=', $this->jalurId);
                })
                ->sum('kuota');

            if (($existingQuota + $this->kuota) > 100) {
                $this->addError('kuota', 'Total kuota semua jalur aktif tidak boleh melebihi 100%. (Total saat ini: ' . $existingQuota . '%)');
                return;
            }
        }

        if ($this->isEditMode) {
            $jalur = JalurPendaftaran::findOrFail($this->jalurId);
            $jalur->update($data);
            $jalur->berkas()->sync($this->selectedBerkas);
            $message = 'Jalur pendaftaran berhasil diperbarui.';
        } else {
            $jalur = JalurPendaftaran::create($data);
            $jalur->berkas()->sync($this->selectedBerkas);
            $message = 'Jalur pendaftaran berhasil ditambahkan.';
        }

        $this->showFormModal = false;
        $this->reset(['nama', 'deskripsi', 'aktif', 'kuota', 'start_date', 'end_date', 'selectedBerkas', 'jalurId', 'isEditMode']);

        session()->flash('message', $message);
    }

    public function confirmDelete($id)
    {
        $jalur = JalurPendaftaran::findOrFail($id);
        $jalur->delete();
        session()->flash('message', 'Jalur pendaftaran berhasil dihapus.');
    }

    public function render()
    {
        $jalurList = JalurPendaftaran::withCount('berkas')
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $allBerkas = Berkas::all();

        return view('livewire.admin.data-jalur', [
            'jalurList' => $jalurList,
            'allBerkas' => $allBerkas,
        ]);
    }
}
