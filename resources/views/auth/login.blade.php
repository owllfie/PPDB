<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to your account">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @if($isOnline)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>
<body class="min-h-screen bg-[#0a0e1a] font-[Inter] flex flex-col items-center justify-center px-4 py-12 relative overflow-y-auto">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-purple-600/8 rounded-full blur-[150px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-[40%] right-[20%] w-[300px] h-[300px] bg-violet-500/5 rounded-full blur-[100px] animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/25 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Welcome Back</h1>
            <p class="text-slate-400 text-sm mt-1">Sign in to your account</p>
        </div>

        <div class="bg-[#111827]/80 backdrop-blur-xl rounded-2xl border border-white/[0.06] shadow-2xl shadow-black/40 p-8">

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
                        <p class="text-green-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1.00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ session('success') }}
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all duration-300"
                                placeholder="you@example.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all duration-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Security Check</label>
                        @if($isOnline)
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                        @else
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 px-4 py-3 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 border border-indigo-500/20 rounded-xl">
                                    <span class="text-indigo-300 font-mono font-bold text-base tracking-wider select-none">{{ $captchaQuestion }}</span>
                                </div>
                                <input type="number" name="captcha" id="captcha" required
                                    class="flex-1 px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 transition-all duration-300"
                                    placeholder="Answer">
                            </div>
                        @endif
                        @error('captcha')
                            <p class="mt-2 text-xs text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                        @error('g-recaptcha-response')
                            <p class="mt-2 text-xs text-red-400 font-medium">Please complete the reCAPTCHA verification.</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Forgot Password?</a>
                    </div>

                <button type="submit" id="login-btn"
                    class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                    Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/[0.06] text-center">
                <p class="text-slate-400 text-sm">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Create one</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
