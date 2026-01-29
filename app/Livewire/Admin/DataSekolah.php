<?php

namespace App\Livewire\Admin;

use App\Models\SekolahDasar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Sekolah Dasar')]
class DataSekolah extends Component
{
    use WithPagination;

    public string $search = '';
    public ?SekolahDasar $selectedSekolah = null;
    public bool $showDetail = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showDetailModal($sekolahId)
    {
        $this->selectedSekolah = SekolahDasar::find($sekolahId);
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->selectedSekolah = null;
    }

    public $form = [
        'npsn' => '',
        'nama' => '',
        'status_sekolah' => 'Negeri',
        'desa_kelurahan' => '',
        'alamat_jalan' => '',
        'generate_account' => true,
    ];
    public $showCreateModal = false;
    public $isEditMode = false;
    public $editId = null;

    public function create()
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->form['status_sekolah'] = 'Negeri';
        $this->form['generate_account'] = true;
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->isEditMode = true;
        $this->editId = $id;

        $sekolah = SekolahDasar::findOrFail($id);
        $this->form = [
            'npsn' => $sekolah->npsn,
            'nama' => $sekolah->nama,
            'status_sekolah' => $sekolah->status_sekolah,
            'desa_kelurahan' => $sekolah->desa_kelurahan ?? '',
            'alamat_jalan' => $sekolah->alamat_jalan ?? '',
            'generate_account' => false,
        ];

        $this->showCreateModal = true;
    }

    public function cancelCreate()
    {
        $this->showCreateModal = false;
        $this->reset(['form', 'isEditMode', 'editId']);
    }

    public function store()
    {
        $this->validate([
            'form.npsn' => 'required|numeric|unique:sekolah_dasar,npsn',
            'form.nama' => 'required|string|max:255',
            'form.status_sekolah' => 'required|in:Negeri,Swasta',
            'form.desa_kelurahan' => 'nullable|string|max:255',
            'form.alamat_jalan' => 'nullable|string|max:500',
        ], [
            'form.npsn.unique' => 'NPSN sudah terdaftar.',
            'form.npsn.numeric' => 'NPSN harus berupa angka.',
            'form.status_sekolah.in' => 'Status sekolah harus Negeri atau Swasta.',
        ]);

        try {
            // Create Sekolah
            $sekolah = SekolahDasar::create([
                'sekolah_id' => (string) \Illuminate\Support\Str::uuid(),
                'npsn' => $this->form['npsn'],
                'nama' => $this->form['nama'],
                'status_sekolah' => $this->form['status_sekolah'],
                'desa_kelurahan' => $this->form['desa_kelurahan'],
                'alamat_jalan' => $this->form['alamat_jalan'],
            ]);

            // Generate Account if requested
            if ($this->form['generate_account']) {
                $this->generateOpsdAccount($sekolah);
            }

            $this->showCreateModal = false;
            $this->reset('form');
            $this->dispatch('import-success', message: 'Data sekolah berhasil ditambahkan.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'form.npsn' => 'required|numeric|unique:sekolah_dasar,npsn,' . $this->editId . ',sekolah_id',
            'form.nama' => 'required|string|max:255',
            'form.status_sekolah' => 'required|in:Negeri,Swasta',
            'form.desa_kelurahan' => 'nullable|string|max:255',
            'form.alamat_jalan' => 'nullable|string|max:500',
        ], [
            'form.npsn.unique' => 'NPSN sudah terdaftar.',
            'form.npsn.numeric' => 'NPSN harus berupa angka.',
            'form.status_sekolah.in' => 'Status sekolah harus Negeri atau Swasta.',
        ]);

        try {
            $sekolah = SekolahDasar::findOrFail($this->editId);
            $sekolah->update([
                'npsn' => $this->form['npsn'],
                'nama' => $this->form['nama'],
                'status_sekolah' => $this->form['status_sekolah'],
                'desa_kelurahan' => $this->form['desa_kelurahan'],
                'alamat_jalan' => $this->form['alamat_jalan'],
            ]);

            // Generate Account if requested (can create new if missing)
            if ($this->form['generate_account']) {
                $this->generateOpsdAccount($sekolah);
            }

            $this->showCreateModal = false;
            $this->reset(['form', 'isEditMode', 'editId']);
            $this->dispatch('import-success', message: 'Data sekolah berhasil diperbarui.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sekolah = SekolahDasar::findOrFail($id);

            // Unlink users first
            User::where('sekolah_id', $id)->update(['sekolah_id' => null]);

            $sekolah->delete();

            $this->dispatch('import-success', message: 'Data sekolah berhasil dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function generateOpsdAccount($sekolah)
    {
        $existingUser = User::where('username', $this->form['npsn'])->first();

        if (!$existingUser) {
            User::create([
                'name' => 'Operator ' . $this->form['npsn'],
                'username' => $this->form['npsn'],
                'password' => Hash::make($this->form['npsn']),
                'role' => 'opsd',
                'sekolah_id' => $sekolah->sekolah_id,
                'is_active' => true,
            ]);
        } else {
            if (!$existingUser->sekolah_id) {
                $existingUser->update(['sekolah_id' => $sekolah->sekolah_id]);
            }
        }
    }

    public function resetData()
    {
        // 1. Delete users with role 'opsd' that are linked to schools
        User::whereNotNull('sekolah_id')->where('role', 'opsd')->delete();

        // 2. Unlink any other users (e.g. admins) to avoid FK constraint issues
        User::whereNotNull('sekolah_id')->update(['sekolah_id' => null]);

        // Disable foreign key checks to allow truncate
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Truncate the table
        SekolahDasar::truncate();

        // Re-enable foreign key checks
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->dispatch('import-success', message: 'Semua data sekolah dan akun OPSD berhasil dihapus.');
    }

    public function render()
    {
        $sekolah = SekolahDasar::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('npsn', 'like', '%' . $this->search . '%')
                    ->orWhere('desa_kelurahan', 'like', '%' . $this->search . '%');
            })
            ->orderBy('nama')
            ->paginate(15);

        return view('livewire.admin.data-sekolah', [
            'sekolahList' => $sekolah,
        ]);
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
