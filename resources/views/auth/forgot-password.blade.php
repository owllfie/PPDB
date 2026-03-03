@extends('auth.layout')

@section('title', 'Lupa Password')

@section('content')
<div class="w-full max-w-md p-8 bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl shadow-2xl">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Recovery</h1>
        <p class="mt-2 text-gray-400">Masukkan email Anda untuk menerima tautan reset password</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('password.method.select') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="space-y-4 mb-2">
            <label class="block text-sm font-medium text-gray-400 mb-2">Select Recovery Method</label>
            
            <div class="grid grid-cols-2 gap-4">
                <label class="relative cursor-pointer group">
                    <input type="radio" name="method" value="email" checked class="peer hidden">
                    <div class="p-4 border border-gray-700/50 rounded-2xl bg-gray-900/30 peer-checked:border-indigo-500 peer-checked:bg-indigo-500/10 hover:border-indigo-500/50 transition-all flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-indigo-400 peer-checked:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-semibold text-gray-400 peer-checked:text-indigo-400">Email</span>
                    </div>
                </label>

                <label class="relative cursor-pointer group">
                    <input type="radio" name="method" value="whatsapp" class="peer hidden">
                    <div class="p-4 border border-gray-700/50 rounded-2xl bg-gray-900/30 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 hover:border-emerald-500/50 transition-all flex flex-col items-center gap-2">
                        <svg class="w-6 h-6 text-gray-500 group-hover:text-emerald-400 peer-checked:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-semibold text-gray-400 peer-checked:text-emerald-400">WhatsApp</span>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input type="email" name="email" required value="{{ old('email') }}" class="block w-full pl-11 pr-4 py-3.5 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all" placeholder="name@example.com">
            </div>
            <p class="mt-2 text-[10px] text-gray-500">For WhatsApp recovery, we will send an OTP to the phone number linked to this email.</p>
            @error('email')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            Lanjutkan
        </button>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-700 text-center">
        <p class="text-sm text-gray-400">
            Kembali ke <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">Sign In</a>
        </p>
    </div>
</div>
@endsection
