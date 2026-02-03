<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Lulus - {{ $siswa->nama }}</title>
    <style>
        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 400;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 500;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-semibold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 600;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-semibold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 700;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-bold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 800;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-bold.ttf') }}") format('truetype');
        }

        * {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 11px;
            line-height: 1.3;
            color: #1e293b;
        }

        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header img {
            height: 60px;
            vertical-align: middle;
        }

        .header-text {
            display: inline-block;
            vertical-align: middle;
            margin-left: 10px;
            text-align: center;
        }

        .header-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            color: #1e293b;
            line-height: 1.1;
        }

        .header-subtitle {
            font-size: 12px;
            margin: 3px 0 0;
            font-weight: 600;
            color: #475569;
        }

        .content-title {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            text-decoration: underline;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-body {
            margin-bottom: 20px;
            text-align: justify;
        }

        .student-info {
            margin: 20px 0;
            padding: 0 20px;
        }

        .table-data {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        .table-data td {
            vertical-align: top;
        }

        .label {
            width: 30%;
            color: #64748b;
            font-weight: 500;
        }

        .colon {
            width: 2%;
            text-align: center;
            color: #64748b;
        }

        .value {
            width: 68%;
            font-weight: 700;
            color: #0f172a;
        }

        .acceptance-box {
            border: 2px solid #16a34a;
            background-color: #f0fdf4;
            color: #166534;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
            border-radius: 8px;
        }

        .acceptance-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .school-name {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        /* Custom list style */
        a {
            color: #0284c7;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="15%" align="center">
                    @if(function_exists('get_setting') && get_setting('app_logo_image') && file_exists(storage_path('app/public/' . get_setting('app_logo_image'))))
                        <img src="{{ storage_path('app/public/' . get_setting('app_logo_image')) }}" alt="Logo">
                    @else
                        <img src="{{ public_path('templates/assets/images/logo.svg') }}" alt="Logo" style="height: 70px;">
                    @endif
                </td>
                <td align="center">
                    <div class="header-title" style="font-size: 20px;">SISTEM PENERIMAAN MURID BARU</div>
                    <div class="header-title">DINAS PENDIDIKAN PEMUDA DAN OLAHRAGA KAB. CIANJUR</div>
                    <div class="header-subtitle">TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</div>
                </td>
                <td width="15%"></td>
            </tr>
        </table>
    </div>

    <div class="content-title">SURAT KETERANGAN LULUS SELEKSI</div>

    <div class="content-body">
        <p>Berdasarkan hasil seleksi Sistem Penerimaan Murid Baru Tingkat SMP Kabupaten Cianjur Tahun Pelajaran
            {{ date('Y') }}/{{ date('Y') + 1 }}, dengan ini panitia menyatakan bahwa:
        </p>
    </div>

    <div class="student-info">
        <table class="table-data">
            <tr>
                <td class="label">Nomor Pendaftaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->nomor_pendaftaran }}</td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td class="value">{{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="colon">:</td>
                <td class="value">{{ $siswa->nisn }}</td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td class="colon">:</td>
                <td class="value">{{ $siswa->sekolah->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jalur Pendaftaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->jalur->nama ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="acceptance-box">
        <div class="acceptance-title">DINYATAKAN LULUS SELEKSI DI:</div>
        <div class="school-name">{{ $sekolah->nama }}</div>
    </div>

    <div class="content-body">
        <p>Harap segera melakukan <strong>DAFTAR ULANG</strong> ke sekolah tujuan dengan membawa bukti kelulusan ini
            dan dokumen persyaratan asli lainnya sesuai dengan jadwal yang telah ditentukan:
        </p>
        @if($pengumuman->daftarUlang)
            <div style="text-align: center; margin: 15px 0;">
                <div style="font-weight: 700; font-size: 14px; color: #1e40af;">
                    JADWAL DAFTAR ULANG:
                </div>
                <div style="font-size: 12px; margin-top: 5px;">
                    Hari/Tanggal: {{ $pengumuman->daftarUlang->tanggal->translatedFormat('l, d F Y') }}
                </div>
                <div style="font-size: 11px; margin-top: 2px;">
                    Pukul {{ $pengumuman->daftarUlang->waktu_mulai->format('H:i') }} -
                    {{ $pengumuman->daftarUlang->waktu_selesai->format('H:i') }} WIB
                </div>
                @if($pengumuman->daftarUlang->lokasi)
                    <div style="font-size: 11px; margin-top: 2px;">
                        Tempat: {{ $pengumuman->daftarUlang->lokasi }}
                    </div>
                @endif
            </div>
        @endif
        <p>
            <em>Catatan: Kelulusan dapat dibatalkan apabila dikemudian hari ditemukan ketidaksesuaian data atau dokumen
                yang dipalsukan.</em>
        </p>
    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td width="130" style="vertical-align: top;">
                    <!-- QR Code -->
                    <div style="width: 120px;">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Verification" width="100%">
                    </div>
                </td>
                <td style="vertical-align: top; padding-left: 10px;">
                    <div style="font-size: 11px; margin-top: 10px;">
                        <ul
                            style="list-style-type: disc; padding-left: 15px; margin: 0; line-height: 1.6; color: #333;">
                            <li><strong>Tanggal Cetak:</strong>
                                {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') }}
                            </li>
                            <li>Surat keterangan ini adalah dokumen sah yang dihasilkan oleh sistem komputer.</li>
                            <li>Tanda tangan basah pejabat tidak diperlukan.</li>
                            <li>Keaslian dokumen ini dapat diverifikasi dengan memindai QR Code di samping.</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>