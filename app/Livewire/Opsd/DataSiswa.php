<?php

namespace App\Livewire\Opsd;

use App\Models\PesertaDidik;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Siswa')]
class DataSiswa extends Component
{
    use WithPagination;

    // public function updated($property, $value)
    // {
    //     // Replaced by specific property hooks
    // }

    public string $search = '';

    protected $listeners = ['siswa-updated' => '$refresh'];

    public function edit($id)
    {
        $this->dispatch('editSiswa', id: $id)->to(EditSiswaModal::class);
    }

    // Logic moved to EditSiswaModal:
    // $isEdit, $editId, $formData, $kecamatanList, $desaList
    // $rules, $selectedKecamatanCode, $selectedDesaCode
    // loadKecamatan, updatedSelectedKecamatanCode, updatedSelectedDesaCode, loadDesa
    // cancelEdit, update

    public function generatePassword(PesertaDidik $siswa)
    {
        // Generate password from birthdate YYYYMMDD if not present
        if (! $siswa->password) {
            $dob = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Ymd') : '12345678';
            $siswa->update([
                'password' => \Illuminate\Support\Facades\Hash::make($dob),
            ]);
        }
    }

    public function cetakKartu($id)
    {
        $siswa = PesertaDidik::findOrFail($id);

        // Ensure password exists
        if (! $siswa->password) {
            $this->generatePassword($siswa);
        }

        return redirect()->route('opsd.cetak-kartu', ['id' => $id]);
    }

    public array $selected = [];

    public bool $selectAll = false;

    // Reset selection when search/page changes
    public function updatingSearch()
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getFilteredQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    // Helper to get query reuse
    protected function getFilteredQuery()
    {
        $user = auth()->user();
        if (! $user->sekolah_id) {
            return PesertaDidik::where('id', 0);
        } // No results

        return PesertaDidik::with(['sekolah', 'pendaftaran'])->where('sekolah_id', $user->sekolah_id)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', "%{$this->search}%")
                        ->orWhere('nisn', 'like', "%{$this->search}%")
                        ->orWhere('nik', 'like', "%{$this->search}%");
                });
            });
    }

    public function cetakMassal()
    {
        if (empty($this->selected)) {
            $this->dispatch('alert', ['type' => 'warning', 'message' => 'Pilih setidaknya satu siswa untuk dicetak.']);

            return;
        }

        // Generate passwords for selected if missing
        PesertaDidik::whereIn('id', $this->selected)
            ->whereNull('password')
            ->get()
            ->each(function ($siswa) {
                $this->generatePassword($siswa);
            });

        return redirect()->route('opsd.cetak-kartu-massal', ['ids' => implode(',', $this->selected)]);
    }

    public function render()
    {
        $user = auth()->user();

        // Ensure user has a linked school
        if (! $user->sekolah_id) {
            return view('livewire.opsd.data-siswa', [
                'pesertaDidikList' => null,
                'hasSchool' => false,
            ]);
        }

        $data = $this->getFilteredQuery()->paginate(15);

        return view('livewire.opsd.data-siswa', [
            'pesertaDidikList' => $data,
            'hasSchool' => true,
            'debugUserSekolahId' => $user->sekolah_id,
        ]);
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
