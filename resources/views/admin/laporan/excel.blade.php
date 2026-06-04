<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    @if($selectedEvent)
    <table>
        <tr>
            <th colspan="5" style="font-size: 14pt; font-weight: bold;">LAPORAN DATA PESERTA EVENT</th>
        </tr>
        <tr>
            <th colspan="5" style="font-size: 12pt; font-weight: bold;">{{ strtoupper($selectedEvent->nama_event) }}</th>
        </tr>
        <tr>
            <td colspan="5">Waktu Ekspor: {{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr><td></td></tr>
        <tr style="background-color: #0d6efd; color: #ffffff;">
            <th style="border: 1px solid #000;">No</th>
            <th style="border: 1px solid #000;">Nama Tim</th>
            <th style="border: 1px solid #000;">Kapten</th>
            <th style="border: 1px solid #000;">Nomor WA</th>
            <th style="border: 1px solid #000;">Anggota Tim</th>
            <th style="border: 1px solid #000;">Biaya Pendaftaran</th>
        </tr>
        @foreach($registrations as $index => $reg)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000;">{{ $reg->nama_tim }}</td>
            <td style="border: 1px solid #000;">{{ $reg->nama_kapten }}</td>
            <td style="border: 1px solid #000;">{{ $reg->nomor_wa }}</td>
            <td style="border: 1px solid #000;">{{ $reg->anggota_tim }}</td>
            <td style="border: 1px solid #000;">{{ $selectedEvent->harga_pendaftaran }}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="5" style="border: 1px solid #000; text-align: right; font-weight: bold;">TOTAL PENDAPATAN</th>
            <th style="border: 1px solid #000; font-weight: bold;">{{ $totalPendapatan }}</th>
        </tr>
    </table>
    @endif
</body>
</html>
