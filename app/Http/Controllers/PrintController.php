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

    public function cetakBukti($id)
    {
        $pendaftaran = \App\Models\Pendaftaran::with(['pesertaDidik', 'sekolah', 'berkas.berkas', 'jalur'])->findOrFail($id);

        // Authorization
        if (auth()->guard('siswa')->check()) {
            if ($pendaftaran->peserta_didik_id != auth()->guard('siswa')->id()) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (!auth()->check()) {
            // If not student and not admin/operator
            abort(403, 'Unauthorized action.');
        }

        // Check verification status
        // User requirement: "jika semua berkas sudah divalidasi"
        $allVerified = true;

        // If no berkas uploaded yet, it is not verified
        if ($pendaftaran->berkas->isEmpty()) {
            $allVerified = false;
        }

        foreach ($pendaftaran->berkas as $file) {
            // Assuming 'approved' or 'verified' are valid statuses. Based on view inspection, 'approved' is used.
            if (!in_array($file->status_berkas, ['approved', 'verified'])) {
                $allVerified = false;
                break;
            }
        }

        if (!$allVerified) {
            abort(403, 'Berkas pendaftaran belum sepenuhnya diverifikasi.');
        }

        $siswa = $pendaftaran->pesertaDidik;
        $sekolah = $pendaftaran->sekolah;

        // Generate QR Code
        // Using BaconQrCode directly as it is installed
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(120),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        // Validasi URL or Data string
        $qrContent = "Bukti Pendaftaran Sah\nNo: {$pendaftaran->nomor_pendaftaran}\nNama: {$siswa->nama}\nNISN: {$siswa->nisn}\nSekolah Tujuan: {$sekolah->nama}\nStatus: Terverifikasi";
        $qrCode = $writer->writeString($qrContent);

        // Convert to Base64 to safely embed in PDF
        $qrCodeBase64 = base64_encode($qrCode);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.bukti-pendaftaran', [
            'pendaftaran' => $pendaftaran,
            'siswa' => $siswa,
            'sekolah' => $sekolah,
            'qrCode' => $qrCodeBase64
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('bukti-pendaftaran-' . $pendaftaran->nomor_pendaftaran . '.pdf');
    }
    public function cetakBuktiLulus($id)
    {
        $pendaftaran = \App\Models\Pendaftaran::with(['pesertaDidik', 'sekolah', 'jalur'])->findOrFail($id);

        // Ensure there is an announcement check
        $pengumuman = \App\Models\Pengumuman::where('pendaftaran_id', $id)->first();

        if (!$pengumuman || $pengumuman->status != 'lulus') {
            abort(403, 'Maaf, Anda tidak memiliki akses untuk mencetak bukti kelulusan.');
        }

        // Authorization
        if (auth()->guard('siswa')->check()) {
            if ($pendaftaran->peserta_didik_id != auth()->guard('siswa')->id()) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $siswa = $pendaftaran->pesertaDidik;
        $sekolah = $pendaftaran->sekolah;

        // Generate QR Code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(120),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);

        $qrContent = "SURAT KETERANGAN LULUS\nNo: {$pendaftaran->nomor_pendaftaran}\nNama: {$siswa->nama}\nNISN: {$siswa->nisn}\nDITERIMA DI: {$sekolah->nama}\nJalur: {$pendaftaran->jalur->nama}";
        $qrCode = $writer->writeString($qrContent);

        $qrCodeBase64 = base64_encode($qrCode);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.bukti-lulus', [
            'pendaftaran' => $pendaftaran,
            'siswa' => $siswa,
            'sekolah' => $sekolah,
            'pengumuman' => $pengumuman,
            'qrCode' => $qrCodeBase64
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('bukti-lulus-' . $pendaftaran->nomor_pendaftaran . '.pdf');
    }
}
