<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h2 { margin: 0 0 12px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .muted { color: #555; font-size: 10px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h2>Daftar Penelitian</h2>
    <div class="muted">Diekspor pada {{ now()->format('d/m/Y H:i') }}</div>
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Peneliti</th>
                <th>Bidang</th>
                <th>Institusi</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Kontak</th>
                <th>Status</th>
                <th>No Surat Rekom</th>
                <th>Tgl Surat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
                <tr>
                    <td>{{ $row->judul }}</td>
                    <td>{{ $row->penulis }}</td>
                    <td>{{ optional($row->field)->nama }}</td>
                    <td>{{ optional($row->institution)->nama }}</td>
                    <td>{{ $formatDate($row->tanggal_mulai) }}</td>
                    <td>{{ $formatDate($row->tanggal_selesai) }}</td>
                    <td>{{ $row->telepon_peneliti }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->nomor_surat_kesbang }}</td>
                    <td>{{ $formatDate($row->tanggal_surat_kesbang) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
