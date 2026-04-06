@php
    $title = $research->judul ?? 'Penelitian';
    $author = $research->penulis ?? optional($research->user)->nama ?? 'Peneliti';
    $downloadLink = route('researches.show', $research->id);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Rekomendasi Siap</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Halo {{ $author }},</p>

    <p>Surat rekomendasi untuk pengajuan penelitian Anda sudah diunggah oleh Kesbangpol.</p>

    <ul>
        <li><strong>Judul:</strong> {{ $title }}</li>
        <li><strong>ID Pengajuan:</strong> {{ $research->id }}</li>
    </ul>

    <p>Silakan masuk ke portal untuk melihat dan mengunduh surat rekomendasi:</p>
    <p><a href="{{ $downloadLink }}" style="color: #0ea5e9; font-weight: bold;">{{ $downloadLink }}</a></p>

    <p>Jika tautan di atas tidak dapat dibuka, salin dan tempel ke peramban Anda setelah login.</p>

    <p>Terima kasih.</p>
    <p>{{ config('app.name', 'Portal Riset BAPPPEDA') }}</p>
</body>
</html>
