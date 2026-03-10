<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekomendasi Jurusan</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <h2 style="margin: 0 0 12px; color: #111827;">Rekomendasi Jurusan</h2>
        <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $user->nama_lengkap ?? $user->email ?? 'Calon Siswa' }}, hasil seleksi Anda memerlukan penyesuaian jurusan. Rekomendasi jurusan: <strong>{{ $recommendedMajor }}</strong>.</p>
        <p style="margin: 0 0 16px; color: #4b5563;">Silakan login kembali dan lakukan daftar ulang melalui tautan berikut.</p>
        <p style="margin: 0; color: #2563eb;"><a href="{{ $actionUrl }}" style="color: #2563eb;">Daftar Ulang</a></p>
    </div>
</body>
</html>
