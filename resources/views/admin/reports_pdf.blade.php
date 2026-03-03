<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendaftaran Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 0; padding: 0; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; }
        .header h1 { margin: 0; font-size: 22px; color: #1e1b4b; }
        .header p { margin: 5px 0 0; color: #6b7280; font-size: 14px; }
        .info { margin-bottom: 25px; background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .info p { margin: 3px 0; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #4f46e5; color: white; border: 1px solid #4338ca; padding: 12px 8px; text-align: left; font-weight: bold; text-transform: uppercase; font-size: 11px; }
        td { border: 1px solid #e5e7eb; padding: 10px 8px; text-align: left; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-approved { background-color: #dcfce7; color: #15803d; }
        .status-rejected { background-color: #fee2e2; color: #b91c1c; }
        .status-pending { background-color: #fef9c3; color: #a16207; }
        .footer { margin-top: 40px; text-align: right; font-size: 11px; color: #9ca3af; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LAPORAN PENDAFTARAN SISWA BARU</h1>
            <p>PPDB Online System - Laporan Rekapitulasi</p>
        </div>

        <div class="info">
            <p><strong>Periode Laporan:</strong> {{ $startDate ?? 'Format Seluruh Waktu' }} s/d {{ $endDate ?? 'Sekarang' }}</p>
            <p><strong>Total Record:</strong> {{ $reportData['total'] }} Siswa Terdaftar</p>
            <p><strong>Status Terverifikasi:</strong> {{ $reportData['total_approved'] }} Diterima | {{ $reportData['total_rejected'] }} Ditolak</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Nama Lengkap</th>
                    <th style="width: 150px;">Tanggal Daftar</th>
                    <th style="width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['raw_data'] as $reg)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="font-weight: bold;">{{ $reg->nama_lengkap }}</td>
                    <td>{{ $reg->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="status-badge {{ $reg->status === 'approved' ? 'status-approved' : ($reg->status === 'rejected' ? 'status-rejected' : 'status-pending') }}">
                            {{ strtoupper($reg->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Dokumen ini dibuat otomatis oleh sistem pada: {{ date('d/m/Y H:i:s') }}</p>
            <p>&copy; {{ date('Y') }} Panitia PPDB Online</p>
        </div>
    </div>
</body>
</html>
