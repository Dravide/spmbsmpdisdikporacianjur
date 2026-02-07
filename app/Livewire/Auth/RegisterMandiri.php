<?php

namespace App\Livewire\Auth;

use App\Models\PesertaDidik;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Pendaftaran Akun Mandiri')]
class RegisterMandiri extends Component
{
    public $step = 1;

    public $totalSteps = 6;

    // Step 1: Identitas Siswa
    public $nama;

    public $nisn;

    public $nik;

    public $jenis_kelamin;

    public $tempat_lahir;

    public $tanggal_lahir;

    public $kebutuhan_khusus;

    // Step 2: Data Orang Tua & Wali
    public $nama_ibu_kandung;

    public $pekerjaan_ibu;

    public $penghasilan_ibu;

    public $nama_ayah;

    public $pekerjaan_ayah;

    public $penghasilan_ayah;

    public $nama_wali;

    public $pekerjaan_wali;

    public $penghasilan_wali;

    // Step 3: Sekolah Asal
    public $sekolah_asal_text;

    public $npsn_sekolah_asal;

    // Step 4: Alamat & Kontak
    public $alamat_jalan;

    public $rt;

    public $rw;

    public $nama_dusun;

    public $desa_kelurahan;

    public $kecamatan;

    public $lintang;

    public $bujur;

    public $no_handphone;

    // Step 5: Data Tambahan (KIP/PKH)
    public $no_KIP;

    public $no_pkh;

    public $flag_pip; // Ya/Tidak

    // Step 6: Password
    public $password;

    public $password_confirmation;

    public $scheduleOpen = true;

    public $scheduleMessage = '';

    public $scheduleStartDate = null;

    public function mount()
    {
        $this->scheduleOpen = \App\Models\Jadwal::isOpen('pendaftaran');
        $this->scheduleMessage = \App\Models\Jadwal::getMessage('pendaftaran');

        if (! $this->scheduleOpen) {
            $jadwal = \App\Models\Jadwal::where('keyword', 'pendaftaran')->first();
            if ($jadwal && $jadwal->aktif && now()->lessThan($jadwal->tanggal_mulai)) {
                $this->scheduleStartDate = $jadwal->tanggal_mulai->toISOString();
            }
        }
    }

    public function nextStep()
    {
        $this->validateStep($this->step);
        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateStep($step)
    {
        if ($step == 1) {
            $this->validate([
                'nama' => 'required|string|max:255',
                'nisn' => 'required|numeric|digits:10|unique:peserta_didiks,nisn',
                'nik' => 'required|numeric|digits:16|unique:peserta_didiks,nik',
                'jenis_kelamin' => 'required|in:L,P',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'kebutuhan_khusus' => 'nullable|string|max:255',
            ], [
                'nisn.unique' => 'NISN ini sudah terdaftar dalam sistem.',
                'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
            ]);
        } elseif ($step == 2) {
            $this->validate([
                'nama_ibu_kandung' => 'required|string|max:255',
                'pekerjaan_ibu' => 'required|string|max:255',
                'penghasilan_ibu' => 'required|string|max:255',
                'nama_ayah' => 'nullable|string|max:255',
                'pekerjaan_ayah' => 'nullable|string|max:255',
                'penghasilan_ayah' => 'nullable|string|max:255',
                'nama_wali' => 'nullable|string|max:255',
                'pekerjaan_wali' => 'nullable|string|max:255',
                'penghasilan_wali' => 'nullable|string|max:255',
            ]);
        } elseif ($step == 3) {
            $this->validate([
                'sekolah_asal_text' => 'required|string|max:255',
                'npsn_sekolah_asal' => 'nullable|numeric',
            ]);
        } elseif ($step == 4) {
            $this->validate([
                'alamat_jalan' => 'required|string|max:500',
                'desa_kelurahan' => 'required|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'rt' => 'required|numeric',
                'rw' => 'required|numeric',
                'nama_dusun' => 'nullable|string|max:255',
                'lintang' => 'nullable|numeric',
                'bujur' => 'nullable|numeric',
            ]);
        } elseif ($step == 5) {
            $this->validate([
                'no_KIP' => 'nullable|string|max:50',
                'no_pkh' => 'nullable|string|max:50',
                'flag_pip' => 'nullable|in:Ya,Tidak',
            ]);
        } elseif ($step == 6) {
            $this->validate([
                'password' => 'required|min:8|confirmed',
            ]);
        }
    }

    public function register()
    {
        if (! \App\Models\Jadwal::isOpen('pendaftaran')) {
            session()->flash('error', \App\Models\Jadwal::getMessage('pendaftaran'));

            return;
        }

        $this->validateStep(6);

        try {
            $siswa = PesertaDidik::create([
                'peserta_didik_id' => Str::uuid(),
                // Identitas
                'nama' => $this->nama,
                'nisn' => $this->nisn,
                'nik' => $this->nik,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'kebutuhan_khusus' => $this->kebutuhan_khusus,

                // Ortu
                'nama_ibu_kandung' => $this->nama_ibu_kandung,
                'pekerjaan_ibu' => $this->pekerjaan_ibu,
                'penghasilan_ibu' => $this->penghasilan_ibu,
                'nama_ayah' => $this->nama_ayah,
                'pekerjaan_ayah' => $this->pekerjaan_ayah,
                'penghasilan_ayah' => $this->penghasilan_ayah,
                'nama_wali' => $this->nama_wali,
                'pekerjaan_wali' => $this->pekerjaan_wali,
                'penghasilan_wali' => $this->penghasilan_wali,

                // Sekolah
                'sekolah_id' => null,

                // Alamat
                'alamat_jalan' => $this->alamat_jalan,
                'desa_kelurahan' => $this->desa_kelurahan,
                'kecamatan' => $this->kecamatan,
                'rt' => $this->rt,
                'rw' => $this->rw,
                'nama_dusun' => $this->nama_dusun,
                'lintang' => $this->lintang,
                'bujur' => $this->bujur,

                // Data Tambahan
                'no_KIP' => $this->no_KIP,
                'no_pkh' => $this->no_pkh,
                'flag_pip' => $this->flag_pip === 'Ya' ? '1' : '0',

                // Account Credentials
                'password' => Hash::make($this->password),

                // External Flags
                'is_external' => true,
                'verification_status' => 'pending',
                'verification_note' => "Sekolah Asal: {$this->sekolah_asal_text} (NPSN: {$this->npsn_sekolah_asal}). No HP: {$this->no_handphone}",
            ]);

            Auth::guard('siswa')->login($siswa);

            return redirect()->route('siswa.dashboard');

        } catch (\Exception $e) {
            Log::error('Register Mandiri Error: '.$e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat pendaftaran: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register-mandiri');
    }
}
