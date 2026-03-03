<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #e2e8f0;
        }
        .container {
            max-width: 520px;
            margin: 40px auto;
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 16px;
            border: 1px solid #334155;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }
        .body {
            padding: 32px 28px;
        }
        .body p {
            font-size: 15px;
            line-height: 1.7;
            color: #94a3b8;
            margin: 0 0 20px 0;
        }
        .otp-box {
            text-align: center;
            margin: 28px 0;
        }
        .otp-code {
            display: inline-block;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 10px;
            color: #a78bfa;
            background: rgba(139, 92, 246, 0.1);
            border: 2px dashed #6366f1;
            border-radius: 12px;
            padding: 16px 32px;
        }
        .footer {
            padding: 20px 28px;
            text-align: center;
            border-top: 1px solid #1e293b;
        }
        .footer p {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verify Your Email</h1>
        </div>
        <div class="body">
            <p>Hello <strong style="color:#e2e8f0;">{{ $username }}</strong>,</p>
            <p>Use the following OTP code to verify your email address. This code will expire in <strong style="color:#e2e8f0;">10 minutes</strong>.</p>
            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>
            <p>If you did not create an account, please ignore this email.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
