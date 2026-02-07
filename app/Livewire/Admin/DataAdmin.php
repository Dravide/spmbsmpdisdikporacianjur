<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Administrator')]
class DataAdmin extends Component
{
    use WithPagination;

    public string $search = '';

    // Form properties
    public $form = [
        'name' => '',
        'username' => '',
        'email' => '',
        'password' => '',
    ];

    public $showCreateModal = false;

    public $isEditMode = false;

    public $editId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->showCreateModal = true;
    }

    public function edit($id)
    {
        $this->reset(['form', 'isEditMode', 'editId']);
        $this->isEditMode = true;
        $this->editId = $id;

        $user = User::findOrFail($id);
        $this->form = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => '', // Don't fill password
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
            'form.name' => 'required|string|max:255',
            'form.username' => 'required|string|max:255|unique:users,username',
            'form.email' => 'nullable|email|max:255|unique:users,email',
            'form.password' => 'required|string|min:6',
        ], [
            'form.username.unique' => 'Username sudah digunakan.',
            'form.email.unique' => 'Email sudah digunakan.',
            'form.username.required' => 'Username wajib diisi.',
            'form.password.required' => 'Password wajib diisi.',
        ]);

        try {
            User::create([
                'name' => $this->form['name'],
                'username' => $this->form['username'],
                'email' => $this->form['email'] ?: null,
                'password' => Hash::make($this->form['password']),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->showCreateModal = false;
            $this->reset('form');
            $this->dispatch('import-success', message: 'Administrator berhasil ditambahkan.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function update()
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.username' => 'required|string|max:255|unique:users,username,'.$this->editId,
            'form.email' => 'nullable|email|max:255|unique:users,email,'.$this->editId,
            'form.password' => 'nullable|string|min:6', // Optional on update
        ], [
            'form.username.unique' => 'Username sudah digunakan.',
            'form.email.unique' => 'Email sudah digunakan.',
        ]);

        try {
            $user = User::findOrFail($this->editId);

            $data = [
                'name' => $this->form['name'],
                'username' => $this->form['username'],
                'email' => $this->form['email'] ?: null,
            ];

            if (! empty($this->form['password'])) {
                $data['password'] = Hash::make($this->form['password']);
            }

            $user->update($data);

            $this->showCreateModal = false;
            $this->reset(['form', 'isEditMode', 'editId']);
            $this->dispatch('import-success', message: 'Administrator berhasil diperbarui.');

        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal memperbarui data: '.$e->getMessage());
        }
    }

    public function resetTwoFactor($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ]);

            $this->dispatch('import-success', message: '2FA berhasil direset untuk pengguna ini.');
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal mereset 2FA: '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($id == Auth::id()) {
            $this->dispatch('import-error', message: 'Anda tidak dapat menghapus akun sendiri.');

            return;
        }

        try {
            $user = User::findOrFail($id);
            $user->delete();

            $this->dispatch('import-success', message: 'Administrator berhasil dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('import-error', message: 'Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function render()
    {
        $users = User::where('role', 'admin')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('username', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.data-admin', [
            'users' => $users,
        ]);
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
