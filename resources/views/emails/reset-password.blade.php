<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #6366f1;">Permintaan Reset Password</h2>
        <p>Halo,</p>
        <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda di PPDB System.</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" style="background-color: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;">Reset Password</a>
        </div>
        <p>Tautan reset password ini akan kedaluwarsa dalam 60 menit.</p>
        <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777;">Jika Anda kesulitan mengklik tombol "Reset Password", salin dan tempelkan URL di bawah ini ke browser Anda:</p>
        <p style="font-size: 12px; color: #777;">{{ $url }}</p>
    </div>
</body>
</html>
