<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Ulang Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <h2 style="margin: 0 0 12px; color: #111827;">Daftar Ulang Berhasil</h2>
        <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $user->nama_lengkap ?? $user->email ?? 'Siswa' }}, daftar ulang Anda berhasil diproses.</p>
        <div style="font-size: 18px; font-weight: bold; color: #111827; background: #f3f4f6; padding: 10px 14px; border-radius: 10px; display: inline-block;">
            NIS: {{ $nis }}
        </div>
    </div>
</body>
</html>
