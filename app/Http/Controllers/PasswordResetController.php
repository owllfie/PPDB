<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;

class PasswordResetController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function selectMethod(ForgotPasswordRequest $request)
    {
        if ($request->input('method') === 'whatsapp') {
            return $this->sendResetOTP($request);
        }

        return $this->sendResetLinkEmail($request);
    }

    public function sendResetOTP(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        if (!$user->no_hp) {
            return back()->withErrors(['email' => 'Akun Anda tidak memiliki nomor WhatsApp yang tertaut. Silakan gunakan metode email atau hubungi administrator.']);
        }

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'phone' => $user->no_hp,
                'token' => $token,
                'otp' => $otp,
                'created_at' => now(),
            ]
        );

        $this->whatsAppService->sendOTP($user->no_hp, $otp);

        return redirect()->route('password.otp.form', ['email' => $request->email])
            ->with('success', 'Kode OTP telah dikirim melalui WhatsApp.');
    }

    public function showOTPForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) return redirect()->route('password.request');

        return view('auth.forgot-password-otp', compact('email'));
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('created_at', '>', now()->subMinutes(10))
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
        }

        return redirect()->route('password.reset', ['token' => $record->token, 'email' => $request->email]);
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $url = route('password.reset', ['token' => $token, 'email' => $request->email]);

        Mail::to($request->email)->send(new ResetPasswordMail($url));

        return back()->with('success', 'Kami telah mengirimkan tautan reset password ke email Anda.');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(ResetPasswordRequest $request)
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Tautan reset password telah kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        
        $user->update([
            'password' => Hash::make($request->password, ['rounds' => 12])
        ]);

        Mail::to($request->email)->send(new \App\Mail\PasswordChangedMail());

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password Anda telah berhasil diperbarui. Silakan login dengan password baru.');
    }
}
