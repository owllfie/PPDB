<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Seleksi</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <h2 style="margin: 0 0 12px; color: #111827;">Hasil Seleksi Akhir</h2>
        <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $user->nama_lengkap ?? $user->email ?? 'Calon Siswa' }}, setelah proses seleksi, Anda belum dapat diterima pada gelombang ini. Terima kasih telah mengikuti proses pendaftaran.</p>
    </div>
</body>
</html>
