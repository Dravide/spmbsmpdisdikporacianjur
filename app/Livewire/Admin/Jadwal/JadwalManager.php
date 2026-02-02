<?php

namespace App\Livewire\Admin\Jadwal;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jadwal;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class JadwalManager extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Manajemen Jadwal SPMB')]

    public $search = '';
    public $perPage = 10;

    // Form Properties
    public $label, $keyword, $tanggal_mulai, $tanggal_selesai, $aktif = true, $deskripsi;
    public $selectedId;
    public $isEdit = false;

    // Validation Rules
    protected function rules()
    {
        return [
            'label' => 'required|string|max:255',
            'keyword' => 'required|string|max:255|unique:jadwals,keyword,' . $this->selectedId,
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
        ];
    }

    public function render()
    {
        $jadwals = Jadwal::query()
            ->where(function ($q) {
                $q->where('label', 'like', '%' . $this->search . '%')
                    ->orWhere('keyword', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.jadwal.jadwal-manager', [
            'jadwals' => $jadwals
        ]);
    }

    // Auto-generate keyword from label if keyword is empty
    public function updatedLabel($value)
    {
        if (empty($this->keyword)) {
            $this->keyword = Str::slug($value);
        }
    }

    public function resetForm()
    {
        $this->reset(['label', 'keyword', 'tanggal_mulai', 'tanggal_selesai', 'aktif', 'deskripsi', 'selectedId', 'isEdit']);
        $this->aktif = true;
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal');
    }

    public function store()
    {
        $this->validate();

        Jadwal::create([
            'label' => $this->label,
            'keyword' => $this->keyword, // Keyword is manual or auto-slug
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'aktif' => $this->aktif,
            'deskripsi' => $this->deskripsi,
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => 'Jadwal berhasil ditambahkan.']);
        $this->resetForm();
    }

    public function edit($id)
    {
        $this->resetForm();
        $jadwal = Jadwal::findOrFail($id);

        $this->selectedId = $id;
        $this->label = $jadwal->label;
        $this->keyword = $jadwal->keyword;
        $this->tanggal_mulai = $jadwal->tanggal_mulai->format('Y-m-d\TH:i');
        $this->tanggal_selesai = $jadwal->tanggal_selesai->format('Y-m-d\TH:i');
        $this->aktif = $jadwal->aktif;
        $this->deskripsi = $jadwal->deskripsi;

        $this->isEdit = true;
        $this->dispatch('open-modal');
    }

    public function update()
    {
        $this->validate();

        $jadwal = Jadwal::findOrFail($this->selectedId);
        $jadwal->update([
            'label' => $this->label,
            'keyword' => $this->keyword,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'aktif' => $this->aktif,
            'deskripsi' => $this->deskripsi,
        ]);

        $this->dispatch('close-modal');
        $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => 'Jadwal berhasil diperbarui.']);
        $this->resetForm();
    }

    public function delete($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => 'Jadwal berhasil dihapus.']);
    }

    public function toggleActive($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update(['aktif' => !$jadwal->aktif]);
        $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => 'Status jadwal diperbarui.']);
    }
}
