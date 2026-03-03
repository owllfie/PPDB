<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function show()
    {
        if (Auth::user()->is_verified) {
            return redirect()->intended('/index');
        }

        return view('auth.verify-email', [
            'email' => Auth::user()->email,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $result = $this->authService->verifyOtp($user, $request->otp);

        if (!$result) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code. Please try again.']);
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Email verified successfully! Please log in to continue.');
    }

    public function resend()
    {
        $user = Auth::user();

        if ($user->is_verified) {
            return redirect()->intended('/index');
        }

        $this->authService->resendOtp($user);

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}
