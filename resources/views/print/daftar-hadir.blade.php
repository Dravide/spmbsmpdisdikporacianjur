<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $sekolah->nama }}</title>
    <style>
        @font-face {
            font-family: 'Plus Jakarta Sans';
            font-weight: 400;
            font-style: normal;
            src: url("{{ public_path('fonts/plus-jakarta-sans-regular.ttf') }}") format('truetype');
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

        * {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 10px;
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
            height: 50px;
            vertical-align: middle;
        }

        .header-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0;
            color: #1e293b;
            line-height: 1.2;
        }

        .header-subtitle {
            font-size: 11px;
            margin: 3px 0 0;
            font-weight: 600;
            color: #475569;
        }

        .content-title {
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            text-decoration: underline;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-box {
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-box table td {
            padding: 2px 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }

        .data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: center;
        }

        .data-table td.center {
            text-align: center;
        }

        .signature-box {
            width: 100%;
            margin-top: 30px;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }


        @page {
            margin: 1.5cm;
        }
    </style>
</head>

<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="12%" align="center">
                    @if(function_exists('get_setting') && get_setting('app_logo_image') && file_exists(storage_path('app/public/' . get_setting('app_logo_image'))))
                        <img src="{{ storage_path('app/public/' . get_setting('app_logo_image')) }}" alt="Logo">
                    @else
                        <img src="{{ public_path('templates/assets/images/logo.svg') }}" alt="Logo" style="height: 50px;">
                    @endif
                </td>
                <td align="center">
                    <div class="header-title">DAFTAR HADIR DAFTAR ULANG</div>
                    <div class="header-title">{{ $sekolah->nama }}</div>
                    <div class="header-subtitle">TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</div>
                </td>
                <td width="12%"></td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i') }} WIB</td>
            </tr>
            @if($dateStart || $dateEnd)
                <tr>
                    <td><strong>Filter Tanggal</strong></td>
                    <td>:</td>
                    <td>
                        @if($dateStart && $dateEnd)
                            {{ \Carbon\Carbon::parse($dateStart)->translatedFormat('d F Y') }} s.d.
                            {{ \Carbon\Carbon::parse($dateEnd)->translatedFormat('d F Y') }}
                        @elseif($dateStart)
                            Mulai {{ \Carbon\Carbon::parse($dateStart)->translatedFormat('d F Y') }}
                        @elseif($dateEnd)
                            Sampai {{ \Carbon\Carbon::parse($dateEnd)->translatedFormat('d F Y') }}
                        @endif
                    </td>
                </tr>
            @endif
            <tr>
                <td><strong>Total Siswa</strong></td>
                <td>:</td>
                <td>{{ $daftarUlangs->count() }} orang</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Siswa</th>
                <th style="width: 80px;">NISN</th>
                <th style="width: 70px;">Jalur</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 60px;">Waktu</th>
                <th style="width: 80px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarUlangs as $index => $data)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $data->pesertaDidik->nama }}</strong>
                        @if($data->nomor_urut)
                            <br><small style="color: #64748b;">Urut: {{ $data->nomor_urut }}</small>
                        @endif
                    </td>
                    <td class="center">{{ $data->pesertaDidik->nisn }}</td>
                    <td class="center">{{ $data->pengumuman->jalur->nama ?? '-' }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i') }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-box">
        <tr>
            <td class="signature-cell"></td>
            <td class="signature-cell">
                <div>{{ $sekolah->nama_kabupaten ?? 'Cianjur' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
                <div style="margin-top: 5px;">Petugas Daftar Ulang</div>
                <div class="signature-line"></div>
                <div style="margin-top: 5px;">NIP. ____________________</div>
            </td>
        </tr>
    </table>
</body>

</html>