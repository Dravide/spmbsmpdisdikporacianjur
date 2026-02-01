<?php

namespace App\Livewire\Admin;

use App\Models\SekolahMenengahPertama;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Daya Tampung')]
class DataDayaTampung extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $showEditModal = false;
    public $editId = null;

    public $form = [
        'nama' => '',
        'jumlah_rombel' => 0,
        'daya_tampung' => 0,
    ];

    public $sudahIsi = 0;
    public $belumIsi = 0;
    public $totalDayaTampung = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        $sekolah = SekolahMenengahPertama::findOrFail($id);
        $this->editId = $id;
        $this->form = [
            'nama' => $sekolah->nama,
            'jumlah_rombel' => $sekolah->jumlah_rombel,
            'daya_tampung' => $sekolah->daya_tampung,
        ];
        $this->showEditModal = true;
    }

    public function closeEdit()
    {
        $this->showEditModal = false;
        $this->reset(['editId', 'form']);
    }

    public function update()
    {
        $this->validate([
            'form.jumlah_rombel' => 'required|integer|min:0',
            'form.daya_tampung' => 'required|integer|min:0',
        ]);

        $sekolah = SekolahMenengahPertama::findOrFail($this->editId);
        $sekolah->update([
            'jumlah_rombel' => $this->form['jumlah_rombel'],
            'daya_tampung' => $this->form['daya_tampung'],
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Data daya tampung berhasil diperbarui.');
    }

    public function toggleLockDataTampung($sekolahId)
    {
        $sekolah = SekolahMenengahPertama::find($sekolahId);
        if ($sekolah) {
            $sekolah->is_locked_daya_tampung = !$sekolah->is_locked_daya_tampung;
            $sekolah->save();

            $status = $sekolah->is_locked_daya_tampung ? 'dikunci' : 'dibuka';
            session()->flash('message', "Data tampung sekolah berhasil $status.");
        }
    }

    public function lockAll()
    {
        SekolahMenengahPertama::where('mode_spmb', 'Full Online')
            ->update(['is_locked_daya_tampung' => true]);

        session()->flash('message', 'Semua data tampung berhasil dikunci.');
    }

    public function unlockAll()
    {
        SekolahMenengahPertama::where('mode_spmb', 'Full Online')
            ->update(['is_locked_daya_tampung' => false]);

        session()->flash('message', 'Semua kunci data tampung berhasil dibuka.');
    }

    public function render()
    {
        $this->sudahIsi = SekolahMenengahPertama::where('daya_tampung', '>', 0)->count();
        $this->belumIsi = SekolahMenengahPertama::where('daya_tampung', 0)->orWhereNull('daya_tampung')->count();
        $this->totalDayaTampung = SekolahMenengahPertama::sum('daya_tampung');

        $sekolahs = SekolahMenengahPertama::query()
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('npsn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, function ($q) {
                if ($this->filterStatus == 'sudah') {
                    $q->where('daya_tampung', '>', 0);
                } elseif ($this->filterStatus == 'belum') {
                    $q->where(function ($sub) {
                        $sub->where('daya_tampung', 0)
                            ->orWhereNull('daya_tampung');
                    });
                }
            })
            ->orderBy('nama')
            ->paginate(15);

        return view('livewire.admin.data-daya-tampung', [
            'sekolahs' => $sekolahs
        ]);
    }

    public function paginationView()
    {
        return 'livewire.custom-pagination';
    }
}
