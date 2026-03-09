@extends('app')

@section('title', 'Student Portal')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Selamat Datang di Portal Pendaftaran</h1>
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

    <div class="mt-10 max-w-3xl mx-auto bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-800">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-indigo-800 dark:text-indigo-300">Informasi Penting</h4>
                <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-400">Pantau inbox Anda secara berkala. Link tes masuk dan link daftar ulang hanya dikirim ke siswa yang lolos pada tahap sebelumnya.</p>
            </div>
        </div>
    </div>

</div>
@endsection
