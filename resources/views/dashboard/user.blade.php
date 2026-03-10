@extends('app')

@section('title', 'Student Portal')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="max-w-6xl mx-auto mb-8">
        <nav class="flex items-center justify-between rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">P</div>
                <div>
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">PPDB System</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Portal Pendaftaran</div>
                </div>
            </div>
            @guest
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                    Login
                </a>
            @endguest
        </nav>
    </div>

    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di Sistem PPDB</h1>
        <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Alur pendaftaran sekarang terdiri dari verifikasi berkas, tes masuk, seleksi akhir, lalu daftar ulang untuk calon siswa yang lulus.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 max-w-6xl mx-auto">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 mb-4">
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">1</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Siapkan Dokumen</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Siapkan dokumen yang dibutuhkan: KK, Ijazah, Akta Lahir, dan Rapor terakhir.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 mb-4">
                <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">2</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Isi Formulir</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi formulir pendaftaran dengan data diri, data orang tua, dan informasi sekolah asal.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/40 mb-4">
                <span class="text-xl font-bold text-amber-600 dark:text-amber-400">3</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Unggah Berkas</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Upload scan/foto KK, Ijazah, dan Akta Lahir dalam format yang jelas dan terbaca.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-900/40 mb-4">
                <span class="text-xl font-bold text-rose-600 dark:text-rose-400">4</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Review & Submit</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Periksa kembali semua data yang telah diisi, pastikan tidak ada kesalahan, lalu kirim formulir.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 mb-4">
                <span class="text-xl font-bold text-purple-600 dark:text-purple-400">5</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tunggu Verifikasi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tim sekolah akan meninjau data Anda. Jika disetujui, link tes masuk akan dikirim ke inbox akun Anda.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 mb-4">
                <span class="text-xl font-bold text-cyan-600 dark:text-cyan-400">6</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tes, Seleksi, Daftar Ulang</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kerjakan tes jika mendapat link, tunggu hasil seleksi akhir, lalu lakukan daftar ulang bila dinyatakan lulus.</p>
        </div>

    </div>

    @guest
        <div class="mt-10 flex justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3 rounded-2xl bg-emerald-600 text-white font-semibold text-lg hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-500/20">
                Daftar sekarang!
            </a>
        </div>
    @endguest

</div>
@endsection
