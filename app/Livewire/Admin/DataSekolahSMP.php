<?php

namespace App\Livewire\Admin;

use App\Models\SekolahMenengahPertama;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Sekolah SMP')]
class DataSekolahSMP extends Component
{
    use WithPagination;

    public string $search = '';

    public ?SekolahMenengahPertama $selectedSekolah = null;

    public bool $showDetail = false;

    // Create/Edit Form
    public $form = [
        'npsn' => '',
        'nama' => '',
        'status_sekolah' => 'Negeri',
        'desa_kelurahan' => '',
        'alamat_jalan' => '',
        'rt' => '',
        'rw' => '',
        'lintang' => '',
        'bujur' => '',
        'kode_wilayah' => '',
        'bentuk_pendidikan_id' => 'SMP',
    ];

    public $showCreateModal = false;

    public $isEditMode = false;

    public $editId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function showDetailModal($sekolahId)
    {
        $this->selectedSekolah = SekolahMenengahPertama::find($sekolahId);
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->selectedSekolah = null;
    }

    // Import functionalities have been moved to ImportSekolahSMP component

    public function create()
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->form['status_sekolah'] = 'Negeri';
        $this->form['mode_spmb'] = 'Semi Online';
        $this->form['bentuk_pendidikan_id'] = 'SMP';
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->isEditMode = true;
        $this->editId = $id;

        $sekolah = SekolahMenengahPertama::findOrFail($id);
        $this->form = [
            'npsn' => $sekolah->npsn,
            'nama' => $sekolah->nama,
            'status_sekolah' => $sekolah->status_sekolah,
            'mode_spmb' => $sekolah->mode_spmb,
            'desa_kelurahan' => $sekolah->desa_kelurahan ?? '',
            'alamat_jalan' => $sekolah->alamat_jalan ?? '',
            'rt' => $sekolah->rt ?? '',
            'rw' => $sekolah->rw ?? '',
            'lintang' => $sekolah->lintang ?? '',
            'bujur' => $sekolah->bujur ?? '',
            'kode_wilayah' => $sekolah->kode_wilayah ?? '',
            'bentuk_pendidikan_id' => $sekolah->bentuk_pendidikan_id ?? 'SMP',
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
            'form.npsn' => 'required|numeric|unique:sekolah_menengah_pertamas,npsn',
            'form.nama' => 'required|string|max:255',
            'form.status_sekolah' => 'required|in:Negeri,Swasta',
            'form.mode_spmb' => 'required|in:Full Online,Semi Online',
            'form.desa_kelurahan' => 'nullable|string|max:255',
            'form.alamat_jalan' => 'nullable|string|max:500',
            'form.rt' => 'nullable|string|max:5',
            'form.rw' => 'nullable|string|max:5',
            'form.lintang' => 'nullable|numeric',
            'form.bujur' => 'nullable|numeric',
            'form.kode_wilayah' => 'nullable|string|max:20',
            'form.bentuk_pendidikan_id' => 'nullable|string|max:50',
        ], [
            'form.npsn.unique' => 'NPSN sudah terdaftar.',
            'form.npsn.numeric' => 'NPSN harus berupa angka.',
            'form.status_sekolah.in' => 'Status sekolah harus Negeri atau Swasta.',
        ]);

        try {
            $sekolah = SekolahMenengahPertama::create([
                'sekolah_id' => (string) \Illuminate\Support\Str::uuid(),
                'npsn' => $this->form['npsn'],
                'nama' => $this->form['nama'],
                'status_sekolah' => $this->form['status_sekolah'],
                'mode_spmb' => $this->form['mode_spmb'],
                'desa_kelurahan' => $this->form['desa_kelurahan'],
                'alamat_jalan' => $this->form['alamat_jalan'],
                'rt' => $this->form['rt'],
                'rw' => $this->form['rw'],
                'lintang' => $this->form['lintang'],
                'bujur' => $this->form['bujur'],
                'kode_wilayah' => $this->form['kode_wilayah'],
                'bentuk_pendidikan_id' => $this->form['bentuk_pendidikan_id'],
            ]);

            $this->showCreateModal = false;
            $this->reset('form');
            $this->dispatch('import-success', message: 'Data sekolah SMP berhasil ditambahkan.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'form.npsn' => 'required|numeric|unique:sekolah_menengah_pertamas,npsn,'.$this->editId.',sekolah_id',
            'form.nama' => 'required|string|max:255',
            'form.status_sekolah' => 'required|in:Negeri,Swasta',
            'form.mode_spmb' => 'required|in:Full Online,Semi Online',
            'form.desa_kelurahan' => 'nullable|string|max:255',
            'form.alamat_jalan' => 'nullable|string|max:500',
            'form.rt' => 'nullable|string|max:5',
            'form.rw' => 'nullable|string|max:5',
            'form.lintang' => 'nullable|numeric',
            'form.bujur' => 'nullable|numeric',
            'form.kode_wilayah' => 'nullable|string|max:20',
            'form.bentuk_pendidikan_id' => 'nullable|string|max:50',
        ], [
            'form.npsn.unique' => 'NPSN sudah terdaftar.',
            'form.npsn.numeric' => 'NPSN harus berupa angka.',
            'form.status_sekolah.in' => 'Status sekolah harus Negeri atau Swasta.',
        ]);

        try {
            $sekolah = SekolahMenengahPertama::findOrFail($this->editId);
            $sekolah->update([
                'npsn' => $this->form['npsn'],
                'nama' => $this->form['nama'],
                'status_sekolah' => $this->form['status_sekolah'],
                'mode_spmb' => $this->form['mode_spmb'],
                'desa_kelurahan' => $this->form['desa_kelurahan'],
                'alamat_jalan' => $this->form['alamat_jalan'],
                'rt' => $this->form['rt'],
                'rw' => $this->form['rw'],
                'lintang' => $this->form['lintang'],
                'bujur' => $this->form['bujur'],
                'kode_wilayah' => $this->form['kode_wilayah'],
                'bentuk_pendidikan_id' => $this->form['bentuk_pendidikan_id'],
            ]);

            $this->showCreateModal = false;
            $this->reset(['form', 'isEditMode', 'editId']);
            $this->dispatch('import-success', message: 'Data sekolah SMP berhasil diperbarui.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $sekolah = SekolahMenengahPertama::findOrFail($id);
            // Unlink users first
            User::where('sekolah_id', $id)->update(['sekolah_id' => null]);
            $sekolah->delete();
            $this->dispatch('import-success', message: 'Data sekolah SMP berhasil dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function resetData()
    {
        User::whereNotNull('sekolah_id')->where('role', 'opsmp')->delete();
        User::whereNotNull('sekolah_id')->update(['sekolah_id' => null]); // Safety for others

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        SekolahMenengahPertama::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->dispatch('import-success', message: 'Semua data sekolah SMP dan akun OPSMP berhasil dihapus.');
    }

    public function generateAccount($id)
    {
        try {
            $sekolah = SekolahMenengahPertama::findOrFail($id);
            $this->generateOpsmpAccount($sekolah);
            $this->dispatch('import-success', message: 'Akun operator (OPSMP) berhasil digenerate.');
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal generate akun: '.$e->getMessage());
        }
    }

    private function generateOpsmpAccount($sekolah)
    {
        $existingUser = User::where('username', $sekolah->npsn)->first();

        if (! $existingUser) {
            User::create([
                'name' => $sekolah->nama,
                'username' => $sekolah->npsn,
                'password' => Hash::make($sekolah->npsn),
                'role' => 'opsmp',
                'sekolah_id' => $sekolah->sekolah_id,
                'is_active' => true,
            ]);
        } else {
            $existingUser->update([
                'name' => $sekolah->nama,
                'password' => Hash::make($sekolah->npsn),
                'sekolah_id' => $sekolah->sekolah_id,
                'role' => 'opsmp',
                'is_active' => true,
            ]);
        }
    }

    public function render()
    {
        $sekolah = SekolahMenengahPertama::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%'.$this->search.'%')
                    ->orWhere('npsn', 'like', '%'.$this->search.'%')
                    ->orWhere('desa_kelurahan', 'like', '%'.$this->search.'%');
            })
            ->with('operator')
            ->orderBy('nama')
            ->paginate(15);

        return view('livewire.admin.data-sekolah-smp', [
            'sekolahList' => $sekolah,
        ]);
    }

    public function resetTwoFactor($sekolahId)
    {
        try {
            $sekolah = SekolahMenengahPertama::findOrFail($sekolahId);
            $user = $sekolah->operator;

            if ($user && $user->two_factor_secret) {
                $user->update([
                    'two_factor_secret' => null,
                    'two_factor_recovery_codes' => null,
                    'two_factor_confirmed_at' => null,
                ]);
                $this->dispatch('import-success', message: '2FA berhasil direset untuk operator sekolah ini.');
            } else {
                $this->dispatch('import-error', message: 'Operator tidak ditemukan atau 2FA belum aktif.');
            }
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal mereset 2FA: '.$e->getMessage());
        }
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
