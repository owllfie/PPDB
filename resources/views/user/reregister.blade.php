@extends('app')

@section('title', 'Daftar Ulang')

@section('content')
<div class="px-4 pt-6 pb-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Ulang</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Form ini hanya untuk calon siswa yang dinyatakan lulus seleksi akhir.</p>
    </div>

    <form action="{{ route('user.reregister.submit', $token) }}" method="POST" class="max-w-4xl space-y-8">
        @csrf
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 space-y-6">
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Identitas Diri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn', $registration->nisn) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $registration->nama_lengkap ?? $registration->user_nama_lengkap) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik', $registration->nik) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $registration->tempat_lahir) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $registration->tanggal_lahir) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                            <option value="Laki-laki" {{ old('jenis_kelamin', $registration->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin', $registration->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Agama</label>
                        <input type="text" name="agama" value="{{ old('agama', $registration->agama) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anak Ke-</label>
                        <input type="number" name="anak_ke-" value="{{ old('anak_ke-', $registration->anak_ke) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Data Orang Tua</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Ayah</label>
                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah', $registration->nama_ayah) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Ibu</label>
                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $registration->nama_ibu) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pekerjaan Ayah</label>
                        <input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $registration->pekerjaan_ayah) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pekerjaan Ibu</label>
                        <input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $registration->pekerjaan_ibu) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Data Akademik & Kontak</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>{{ old('alamat', $registration->alamat_lengkap) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sekolah Asal</label>
                        <input type="text" name="sekolah_asal" value="{{ old('sekolah_asal', $registration->sekolah_asal) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nilai Rapor</label>
                        <input type="number" name="nilai_rapor" value="{{ old('nilai_rapor', $registration->nilai_rapor) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilihan Jurusan</label>
                        <select name="id_jurusan" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id_jurusan }}" {{ (string) old('id_jurusan', $registration->id_jurusan) === (string) $jurusan->id_jurusan ? 'selected' : '' }}>
                                    {{ $jurusan->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">No HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $registration->no_hp ?? $registration->user_no_hp) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $registration->email ?? $registration->user_email) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-3" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
            Simpan Daftar Ulang
        </button>
    </form>
</div>
@endsection
