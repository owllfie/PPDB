<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Verify your email address">
    <title>Verify Email - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .otp-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
        .otp-input {
            caret-color: #818cf8;
        }
    </style>
</head>
<body class="min-h-screen bg-[#0a0e1a] font-[Inter] flex flex-col items-center justify-center px-4 py-12 relative overflow-y-auto">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[20%] w-[400px] h-[400px] bg-emerald-600/8 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-15%] right-[10%] w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[140px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-[60%] left-[-5%] w-[300px] h-[300px] bg-purple-500/6 rounded-full blur-[100px] animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Verify Your Email</h1>
            <p class="text-slate-400 text-sm mt-1">We sent a 6-digit code to</p>
            <p class="text-indigo-400 font-medium text-sm mt-0.5">{{ $email }}</p>
        </div>

        <div class="bg-[#111827]/80 backdrop-blur-xl rounded-2xl border border-white/[0.06] shadow-2xl shadow-black/40 p-8">

            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <p class="text-emerald-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-400 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('verify.email.submit') }}" class="space-y-6" id="otp-form">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-4 text-center">Enter Verification Code</label>
                    <div class="flex justify-center gap-3" id="otp-container">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="0" inputmode="numeric" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="1" inputmode="numeric" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="2" inputmode="numeric" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="3" inputmode="numeric" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="4" inputmode="numeric" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 text-center text-xl font-bold bg-white/[0.04] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:border-indigo-500/40 transition-all duration-300" data-index="5" inputmode="numeric" autocomplete="off">
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden">
                </div>

                <button type="submit" id="verify-btn"
                    class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]">
                    Verify Email
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-white/[0.06] flex items-center justify-between">
                <p class="text-slate-400 text-sm">Didn't receive the code?</p>
                <form method="POST" action="{{ route('verify.email.resend') }}">
                    @csrf
                    <button type="submit" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium transition-colors">
                        Resend OTP
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-500 hover:text-slate-400 text-sm transition-colors">
                    Sign out and use a different account
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenInput = document.getElementById('otp-hidden');
            const form = document.getElementById('otp-form');

            function updateHiddenInput() {
                let otp = '';
                inputs.forEach(function(input) {
                    otp += input.value;
                });
                hiddenInput.value = otp;
            }

            inputs.forEach(function(input, index) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;

                    if (value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }

                    updateHiddenInput();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                        inputs[index - 1].value = '';
                        updateHiddenInput();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);

                    for (let i = 0; i < pasteData.length; i++) {
                        if (inputs[i]) {
                            inputs[i].value = pasteData[i];
                        }
                    }

                    let focusIndex = Math.min(pasteData.length, inputs.length - 1);
                    inputs[focusIndex].focus();
                    updateHiddenInput();
                });
            });

            if (inputs[0]) {
                inputs[0].focus();
            }

            form.addEventListener('submit', function() {
                updateHiddenInput();
            });
        });
    </script>

</body>
</html>
