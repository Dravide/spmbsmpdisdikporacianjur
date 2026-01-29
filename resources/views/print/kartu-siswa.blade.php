<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta SPMB - {{ $siswa->nama }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

        .card-container {
            width: 165mm;
            height: 107.5mm;
            position: relative;
            background: white;
            background-image: url('{{ asset("templates/assets/images/auth/auth-cover-bg.png") }}');
            background-size: cover;
            background-position: center;
            box-sizing: border-box;
            border: 1px dashed #e2e8f0;
            /* Helper border for cutting */
        }

        .header {
            display: flex;
            /* dompdf has limited flex support, table fallback might be needed if flex fails, but simple flex usually ok in newer versions */
            /* Using table layout for better compatibility in PDFs */
            padding: 20px 25px 10px;
            border-bottom: 2px solid #3b82f6;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-logo {
            width: 60px;
            vertical-align: middle;
        }

        .header-logo img {
            height: 50px;
            object-fit: contain;
        }

        .header-text {
            vertical-align: middle;
            padding-left: 15px;
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e40af;
            /* Darker blue */
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

        .info-table {
            width: 100%;
        }

        .info-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            padding-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            padding-bottom: 15px;
        }

        .credentials-box {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 5px;
        }

        .cred-table td {
            vertical-align: middle;
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
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <div class="card-container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-logo">
                        @if(function_exists('get_setting') && get_setting('app_logo_image') && file_exists(storage_path('app/public/' . get_setting('app_logo_image'))))
                            <img src="{{ storage_path('app/public/' . get_setting('app_logo_image')) }}" alt="Logo">
                        @else
                            <img src="{{ public_path('templates/assets/images/logo.svg') }}" alt="Logo">
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
            <table class="info-table" style="width: 100%;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value" style="font-size: 18px;">{{ $siswa->nama }}</div>

                        <div class="info-label">Tempat, Tanggal Lahir</div>
                        <div class="info-value">{{ $siswa->tempat_lahir }},
                            {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d F Y') : '-' }}
                        </div>
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
                                    <td class="cred-value">{{ $password }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div style="float: left;">Kartu ini sah dan dicetak oleh sistem.</div>
            <div style="float: right;">Dicetak: {{ date('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>

</html>