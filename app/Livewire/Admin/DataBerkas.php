<?php

namespace App\Livewire\Admin;

use App\Models\Berkas;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Data Berkas Persyaratan')]
class DataBerkas extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Form fields
    public $nama, $deskripsi, $jenis = 'Berkas Umum', $is_required = true, $max_size_kb = 2048;
    public $berkasId;
    public $isEditMode = false;
    public $showFormModal = false;

    // Validation rules
    protected $rules = [
        'nama' => 'required|string|max:255',
        'deskripsi' => 'nullable|string',
        'jenis' => 'required|in:Berkas Umum,Berkas Khusus,Berkas Tambahan',
        'is_required' => 'boolean',
        'max_size_kb' => 'required|integer|min:100|max:10240',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['nama', 'deskripsi', 'jenis', 'is_required', 'max_size_kb', 'berkasId', 'isEditMode']);
        $this->jenis = 'Berkas Umum';
        $this->is_required = true;
        $this->max_size_kb = 2048;
        $this->isEditMode = false;
        $this->showFormModal = true;
    }

    public function edit($id)
    {
        $berkas = Berkas::findOrFail($id);
        $this->berkasId = $berkas->id;
        $this->nama = $berkas->nama;
        $this->deskripsi = $berkas->deskripsi;
        $this->jenis = $berkas->jenis;
        $this->is_required = (bool) $berkas->is_required;
        $this->max_size_kb = $berkas->max_size_kb ?? 2048;

        $this->isEditMode = true;
        $this->showFormModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'jenis' => $this->jenis,
            'is_required' => $this->is_required,
            'max_size_kb' => $this->max_size_kb,
        ];

        if ($this->isEditMode) {
            $berkas = Berkas::findOrFail($this->berkasId);
            $berkas->update($data);
            $message = 'Berkas berhasil diperbarui.';
        } else {
            Berkas::create($data);
            $message = 'Berkas berhasil ditambahkan.';
        }

        $this->showFormModal = false;
        $this->reset(['nama', 'deskripsi', 'is_required', 'max_size_kb', 'berkasId', 'isEditMode']);

        // Dispatch success notification via browser event or standard session flash
        session()->flash('message', $message);
    }

    public function confirmDelete($id)
    {
        // Simple delete for now
        $berkas = Berkas::findOrFail($id);
        $berkas->delete();
        session()->flash('message', 'Berkas berhasil dihapus.');
    }

    public function render()
    {
        $berkas = Berkas::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.data-berkas', [
            'berkasList' => $berkas
        ]);
    }
}
