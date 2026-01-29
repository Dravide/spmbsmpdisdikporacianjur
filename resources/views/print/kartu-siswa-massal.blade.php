<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta Massal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        @page {
            size: 165mm 107.5mm;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #1e293b;
        }

        .page-break {
            page-break-after: always;
        }

        .card-container {
            width: 165mm;
            height: 107.5mm;
            position: relative;
            background: white;
            /* box-sizing: border-box; */
            border: 1px dashed #e2e8f0;
            /* Cut line */
            overflow: hidden;
        }

        /* Common styles */
        .header {
            padding: 20px 25px 10px;
            border-bottom: 2px solid #3b82f6;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo img {
            height: 50px;
            object-fit: contain;
        }

        .header-text {
            padding-left: 15px;
            vertical-align: middle;
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e40af;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.2;
        }

        .header-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }

        .content {
            padding: 20px 30px;
        }

        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .credentials-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
        }

        .cred-table {
            width: 100%;
        }

        .cred-label {
            font-size: 11px;
            color: #475569;
            font-weight: 600;
            width: 80px;
        }

        .cred-value {
            font-size: 16px;
            font-weight: 700;
            color: #0284c7;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .footer {
            position: absolute;
            bottom: 15px;
            left: 25px;
            right: 25px;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    @foreach($siswas as $siswa)
        <div class="card-container">
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td style="width: 60px;">
                            @if(function_exists('get_setting') && get_setting('app_logo_image') && file_exists(storage_path('app/public/' . get_setting('app_logo_image'))))
                                <img src="{{ storage_path('app/public/' . get_setting('app_logo_image')) }}" alt="Logo"
                                    style="height: 50px;">
                            @else
                                <img src="{{ public_path('templates/assets/images/logo.svg') }}" alt="Logo"
                                    style="height: 50px;">
                            @endif
                        </td>
                        <td class="header-text">
                            <div style="margin-bottom: 0;">
                                @if(function_exists('get_setting') && get_setting('app_logo_text_image') && file_exists(storage_path('app/public/' . get_setting('app_logo_text_image'))))
                                    <img src="{{ storage_path('app/public/' . get_setting('app_logo_text_image')) }}"
                                        alt="Brand" style="height: 30px;">
                                @else
                                    <span style="font-weight: 700; color: #64748b; font-size: 14px;">
                                        {{ function_exists('get_setting') ? get_setting('app_logo_text', 'SPMB') : 'SPMB' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin-top: 15px; margin-bottom: 5px;">
                 <h1 class="header-title">KARTU AKUN SPMB</h1>
                 <div class="header-subtitle">{{ $sekolah->nama ?? 'DINAS PENDIDIKAN DAN KEBUDAYAAN' }}</div>
            </div>

            <div class="content">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 58%; vertical-align: top; padding-right: 2%;">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value" style="font-size: 18px;">{{ $siswa->nama }}</div>

                            <div class="info-label">Tempat, Tanggal Lahir</div>
                            <div class="info-value">{{ $siswa->tempat_lahir }},
                                {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}
                            </div>

                            <div class="info-label">Asal Sekolah</div>
                            <div class="info-value" style="margin-bottom: 0;">{{ $sekolah->nama ?? '-' }}</div>
                        </td>
                        <td style="width: 40%; vertical-align: top;">
                            <div class="credentials-box">
                                <table class="cred-table">
                                    <tr>
                                        <td class="cred-label">NISN</td>
                                        <td class="cred-value">{{ $siswa->nisn }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="height: 8px;"></td>
                                    </tr>
                                    <tr>
                                        <td class="cred-label">PASSWORD</td>
                                        <td class="cred-value">{{ $siswa->display_password }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                <div style="float: left;">Kartu sah sistem SPMB.</div>
                <div style="float: right;">Dicetak: {{ date('d/m/Y H:i') }}</div>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>