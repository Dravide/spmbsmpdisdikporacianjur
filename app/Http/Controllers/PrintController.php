<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function cetakKartu($id)
    {
        // Check if student is logged in
        if (auth()->guard('siswa')->check()) {
            if (auth()->guard('siswa')->id() != $id) {
                abort(403, 'Unauthorized action.');
            }
            $siswa = \App\Models\PesertaDidik::findOrFail($id);
            $sekolah = \App\Models\SekolahDasar::where('sekolah_id', $siswa->sekolah_id)->first();
        } else {
            // Operator logic
            $user = auth()->user();
            $siswa = \App\Models\PesertaDidik::where('id', $id)
                ->where('sekolah_id', $user->sekolah_id)
                ->firstOrFail();
            $sekolah = \App\Models\SekolahDasar::where('sekolah_id', $user->sekolah_id)->first();
        }

        $this->ensurePasswordGenerated($siswa);

        $password = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Ymd') : '12345678';



        // Custom paper size: 165mm x 107.5mm (Landscape)
        // 165mm = 467.72 pt
        // 107.5mm = 304.72 pt
        $customPaper = [0, 0, 467.72, 304.72];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.kartu-siswa', [
            'siswa' => $siswa,
            'sekolah' => $sekolah,
            'password' => $password
        ])->setPaper($customPaper, 'landscape');

        return $pdf->stream('kartu-peserta-' . $siswa->nisn . '.pdf');
    }

    public function cetakKartuMassal(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $ids = explode(',', $request->query('ids', ''));

        $siswas = \App\Models\PesertaDidik::whereIn('id', $ids)
            ->where('sekolah_id', $user->sekolah_id)
            ->get();

        if ($siswas->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa yang dipilih.');
        }

        foreach ($siswas as $siswa) {
            $this->ensurePasswordGenerated($siswa);
            $siswa->display_password = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Ymd') : '12345678';
        }

        $sekolah = \App\Models\SekolahDasar::where('sekolah_id', $user->sekolah_id)->first();

        // Custom paper size: 165mm x 107.5mm (Landscape)
        $customPaper = [0, 0, 467.72, 304.72];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.kartu-siswa-massal', [
            'siswas' => $siswas,
            'sekolah' => $sekolah
        ])->setPaper($customPaper, 'landscape');

        return $pdf->stream('kartu-peserta-massal.pdf');
    }

    private function ensurePasswordGenerated($siswa)
    {
        if (!$siswa->password) {
            $dob = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Ymd') : '12345678';
            $siswa->update([
                'password' => \Illuminate\Support\Facades\Hash::make($dob)
            ]);
        }
    }
}
