@extends('auth.layout')

@section('title', 'Reset Password')

@section('content')
<div class="w-full max-w-md p-8 bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl shadow-2xl">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Reset Password</h1>
        <p class="mt-2 text-gray-400">Perbarui kredensial akun Anda</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Email Address</label>
            <input type="email" name="email" required value="{{ $email ?? old('email') }}" class="block w-full px-4 py-3.5 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all">
            @error('email')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">New Password</label>
            <input type="password" name="password" required class="block w-full px-4 py-3.5 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all" placeholder="••••••••">
            @error('password')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-400 mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" required class="block w-full px-4 py-3.5 bg-gray-900/50 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-all" placeholder="••••••••">
        </div>

        <button type="submit" class="w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/25 transition-all active:scale-[0.98]">
            Update Password
        </button>
    </form>
</div>
@endsection
