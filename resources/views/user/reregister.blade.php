@extends('app')

@section('title', 'Daftar Ulang')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Ulang</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Form ini hanya untuk calon siswa yang dinyatakan lulus seleksi akhir.</p>
    </div>

    <form action="{{ route('user.reregister.submit', $token) }}" method="POST" class="max-w-3xl space-y-6">
        @csrf
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $registration->nama_lengkap) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $registration->email) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">No HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $registration->no_hp) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat</label>
                <textarea name="alamat" rows="4" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>{{ old('alamat', $registration->alamat_lengkap) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NISN</label>
                <input type="text" value="{{ $registration->nisn }}" class="w-full rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-400 px-4 py-3" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jurusan</label>
                <select name="id_jurusan" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id_jurusan }}" {{ (string) old('id_jurusan', $registration->id_jurusan) === (string) $jurusan->id_jurusan ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
            Simpan Daftar Ulang
        </button>
    </form>
</div>
@endsection
