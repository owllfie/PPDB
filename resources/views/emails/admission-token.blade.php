<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Token Login</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb;">
        <h2 style="margin: 0 0 12px; color: #111827;">Akun Anda Disetujui</h2>
        <p style="margin: 0 0 16px; color: #4b5563;">Halo {{ $user->nama_lengkap ?? $user->email ?? 'Calon Siswa' }}, akun Anda telah disetujui. Gunakan token berikut untuk login.</p>
        <div style="font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #111827; background: #f3f4f6; padding: 12px 16px; border-radius: 10px; text-align: center;">
            {{ $token }}
        </div>
        <p style="margin: 16px 0 0; color: #6b7280;">Token ini hanya berlaku untuk satu kali login.</p>
    </div>
</body>
</html>
