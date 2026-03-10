<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        $isOnline = $this->authService->isOnline();
        $captcha = $this->authService->generateCaptcha();
        session([
            'captcha_answer' => $captcha['answer'],
            'is_online_captcha' => $isOnline
        ]);
        
        return view('auth.login', [
            'captchaQuestion' => $captcha['question'],
            'isOnline' => $isOnline
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $files = ['kk', 'ijazah', 'akta_lahir', 'rapor', 'pas_foto'];
        foreach ($files as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('registration_docs', 'public');
            }
        }

        $user = $this->authService->registerUser($data);

        Auth::login($user);

        return redirect()->route('verify.email');
    }

    public function login(Request $request)
    {
        $isOnline = session('is_online_captcha', false);

        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        if ($isOnline) {
            $rules['g-recaptcha-response'] = ['required'];
        } else {
            $rules['captcha'] = ['required', 'integer'];
        }

        $request->validate($rules);

        if ($isOnline) {
            if (!$this->authService->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                return back()->withErrors(['captcha' => 'reCAPTCHA verification failed.'])->withInput();
            }
        } else {
            $captchaAnswer = session('captcha_answer');
            if ((int) $request->captcha !== (int) $captchaAnswer) {
                return back()->withErrors(['captcha' => 'Incorrect CAPTCHA answer.'])->withInput();
            }
        }

        $user = $this->authService->attemptLogin($request->email, $request->password);

        if ($user === false) {
            return back()->withErrors(['email' => 'Invalid credentials or account not verified.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
