<!DOCTYPE html>
<html>

<head>
    <title>Daftar Hadir Daftar Ulang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .qr-code {
            width: 50px;
            height: 50px;
        }

        .ttd-box {
            height: 50px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>DAFTAR HADIR DAFTAR ULANG PESERTA DIDIK BARU</h3>
        <p>TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</p>
        <p>{{ $sekolah->nama }}</p>
    </div>

    @foreach($groupedData as $date => $timeGroups)
        @foreach($timeGroups as $timeRange => $rows)
            <div style="margin-bottom: 20px; page-break-inside: avoid;">
                <table style="width: 100%; border: none; margin-bottom: 5px;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 0; font-weight: bold;">
                            Hari/Tanggal: {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                        </td>
                        <td style="border: none; padding: 0; text-align: right; font-weight: bold;">
                            Sesi: {{ $timeRange }}
                        </td>
                    </tr>
                </table>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%">NO</th>
                            <th style="width: 10%">QR</th>
                            <th style="width: 15%">NOMOR PESERTA</th>
                            <th style="width: 25%">NAMA MURID</th>
                            <th style="width: 20%">ASAL SEKOLAH</th>
                            <th style="width: 10%">JALUR</th>
                            <th style="width: 15%">TANDA TANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $key => $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <img src="data:image/svg+xml;base64,{{ $row['qr_code'] }}" class="qr-code">
                                </td>
                                <td class="text-center">{{ $row['nomor_peserta'] }}</td>
                                <td>{{ $row['nama'] }}</td>
                                <td>{{ $row['asal_sekolah'] }}</td>
                                <td class="text-center">{{ $row['jalur'] }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$loop->parent->last || !$loop->last)
                <div style="page-break-after: always;"></div>
            @endif
        @endforeach
    @endforeach

    <div class="footer">
        <p>Cianjur, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
        <p>Panitia PPDB</p>
        <br><br><br>
        <p>(................................................)</p>
    </div>
</body>

</html>