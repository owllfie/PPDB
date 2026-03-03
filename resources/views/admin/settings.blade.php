@extends('app')

@section('title', 'Web Settings')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Web Settings</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage school branding and site information</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 max-w-2xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $setting->nama_sekolah ?? '') }}" maxlength="50" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kepala Sekolah</label>
                    <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah', $setting->kepala_sekolah ?? '') }}" maxlength="50" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="3" maxlength="255" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('alamat', $setting->alamat ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nomor Kontak</label>
                    <input type="text" name="nomor_kontak" value="{{ old('nomor_kontak', $setting->nomor_kontak ?? '') }}" maxlength="50" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Logo Sekolah</label>
                    @if(!empty($setting->logo_sekolah))
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $setting->logo_sekolah) }}" alt="Logo" class="h-16 w-16 object-contain rounded-lg border border-gray-200 dark:border-gray-600">
                        </div>
                    @endif
                    <input type="file" name="logo_sekolah" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 dark:focus:ring-indigo-800 transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
