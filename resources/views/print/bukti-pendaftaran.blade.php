<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran - {{ $siswa->nama }}</title>
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
            font-family: "Plus Jakarta Sans", sans-serif !important;
            font-size: 11px;
            line-height: 1.3;
            color: #1e293b;
        }

        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            /* Reduced */
            margin-bottom: 15px;
            /* Reduced */
        }

        .header img {
            height: 60px;
            /* Reduced */
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
            /* Changed to 700 to match import */
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
            /* Reduced */
            font-weight: 700;

            margin-bottom: 15px;
            /* Reduced */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section {
            margin-bottom: 15px;
            /* Reduced */
        }

        .section-title {
            font-weight: 700;
            background-color: #f1f5f9;
            padding: 5px 8px;
            /* Reduced */
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 5px;
            /* Reduced */
            font-size: 11px;
            /* Reduced */
            color: #334155;
            text-transform: uppercase;
        }

        .table-data {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 2px;
            /* Reduced */
        }

        .table-data td {
            vertical-align: top;
            padding: 1px;
            /* Reduced */
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
            font-weight: 600;
            color: #0f172a;
        }

        .box-info {
            border: 2px dashed #94a3b8;
            padding: 10px;
            /* Reduced */
            margin-top: 15px;
            /* Reduced from 30px */
            text-align: center;
            font-weight: 700;
            background-color: #f8fafc;
            color: #334155;
            font-size: 12px;
            border-radius: 8px;
        }

        .footer {
            margin-top: 20px;
            /* Reduced from 40px */
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .signature {
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        .signature-space {
            height: 60px;
        }

        /* Custom list style for footer links */
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

    <div class="content-title">TANDA BUKTI PENDAFTARAN</div>

    <div class="section">
        <div class="section-title">A. STATUS PENDAFTARAN</div>
        <table class="table-data">
            <tr>
                <td class="label">Nomor Pendaftaran</td>
                <td class="colon">:</td>
                <td class="value" style="font-size: 14px; font-weight: 800; letter-spacing: 0.5px;">
                    {{ $pendaftaran->nomor_pendaftaran }}
                </td>
            </tr>
            <tr>
                <td class="label">Jalur Pendaftaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->jalur->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Sekolah Tujuan</td>
                <td class="colon">:</td>
                <td class="value">{{ $sekolah->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Daftar</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $pendaftaran->submitted_at ? \Carbon\Carbon::parse($pendaftaran->submitted_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : ($pendaftaran->tanggal_daftar ? \Carbon\Carbon::parse($pendaftaran->tanggal_daftar)->locale('id')->translatedFormat('d F Y') : '-') }}
                </td>
            </tr>
            <tr>
                <td class="label">Status Verifikasi</td>
                <td class="colon">:</td>
                <td class="value" style="color: #16a34a; text-transform: uppercase;">Sudah Diverifikasi</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">B. DATA CALON PESERTA DIDIK</div>
        <table class="table-data">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pesertaDidik->nama }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pesertaDidik->nisn }}</td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pesertaDidik->nik }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pesertaDidik->tempat_lahir }},
                    {{ $pendaftaran->pesertaDidik->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->pesertaDidik->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td class="colon">:</td>
                <td class="value">{{ $pendaftaran->pesertaDidik->sekolah->nama ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">C. STATUS KELENGKAPAN BERKAS</div>
        <table width="100%" cellspacing="0" cellpadding="8" border="1"
            style="border-collapse: collapse; border-color: #cbd5e1; border-style: solid;">
            <thead>
                <tr style="background-color: #f8fafc; color: #24282cff;">
                    <th align="left" style="font-weight: 700;">Nama Berkas</th>
                    <th align="center" width="120" style="font-weight: 700;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendaftaran->berkas as $file)
                    <tr>
                        <td style="color: #475569; padding: 4px 8px; vertical-align: middle;">
                            {{ $file->berkas->nama ?? 'Berkas' }}
                        </td>
                        <td align="center" style="padding: 4px 8px; vertical-align: middle;">
                            @if($file->status_berkas == 'approved' || $file->status_berkas == 'verified')
                                <span style="color: #16a34a; font-weight: 600;">&#10003; Valid</span>
                            @else
                                {{ ucfirst($file->status_berkas) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="box-info">
        BUKTI PENDAFTARAN INI HARAP DISIMPAN DAN DIBAWA SAAT DAFTAR ULANG
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
                            <li>Untuk mendapatkan informasi lebih lanjut, silakan follow instagram <a
                                    href="https://instagram.com/disdikpora.cianjur"
                                    style="color: #0088cc; text-decoration: none;">@disdikpora.cianjur</a></li>
                            <li>Atau kunjungi website resmi SPMB SMP DISDIKPORA Kab. Cianjur <a
                                    href="{{ config('app.url') }}"
                                    style="color: #0088cc; text-decoration: none;">{{ config('app.url') }}</a></li>
                        </ul>
                    </div>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>