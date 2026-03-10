<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Buat akun baru">
    <title>Daftar - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-[#0a0e1a] font-[Inter] flex flex-col items-center justify-center px-4 py-12 relative overflow-y-auto">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-15%] right-[-10%] w-[500px] h-[500px] bg-purple-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-15%] left-[-10%] w-[600px] h-[600px] bg-indigo-600/8 rounded-full blur-[150px] animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute top-[50%] left-[15%] w-[250px] h-[250px] bg-cyan-500/5 rounded-full blur-[100px] animate-pulse" style="animation-delay: 3s;"></div>
    </div>

    <div class="w-full max-w-4xl relative z-10">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 shadow-lg shadow-purple-500/25 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Buat Akun</h1>
            <p class="text-slate-400 text-sm mt-1">Lengkapi data untuk pendaftaran</p>
        </div>

        <div class="bg-[#111827]/80 backdrop-blur-xl rounded-2xl border border-white/[0.06] shadow-2xl shadow-black/40 p-8">

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

            <div class="flex items-center justify-between mb-6 text-xs text-slate-400">
                <span id="step-label">Langkah 1 dari 3</span>
                <div class="flex items-center gap-2">
                    <span class="w-20 h-1.5 rounded-full bg-purple-500/40" id="step-bar-1"></span>
                    <span class="w-20 h-1.5 rounded-full bg-white/[0.08]" id="step-bar-2"></span>
                    <span class="w-20 h-1.5 rounded-full bg-white/[0.08]" id="step-bar-3"></span>
                </div>
            </div>

            <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data" class="space-y-8" id="register-form">
                @csrf

                <div id="step-1" class="space-y-6">
                    <h2 class="text-sm font-semibold text-slate-200">Informasi Akun</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <input type="text" name="username" id="username" value="{{ old('username') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300"
                                    placeholder="johndoe">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300"
                                    placeholder="you@example.com">
                            </div>
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-slate-300 mb-2">No. HP</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                            <input type="hidden" name="role" value="1">
                            <input type="text" value="Siswa" disabled
                                class="w-full px-4 py-3 bg-white/[0.02] border border-white/[0.06] rounded-xl text-slate-400 text-sm">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300"
                                    placeholder="********">
                            </div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300"
                                    placeholder="********">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="step-2" class="space-y-6 hidden">
                    <h2 class="text-sm font-semibold text-slate-200">Identitas Diri</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nisn" class="block text-sm font-medium text-slate-300 mb-2">NISN</label>
                            <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="nik" class="block text-sm font-medium text-slate-300 mb-2">NIK</label>
                            <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="tempat_lahir" class="block text-sm font-medium text-slate-300 mb-2">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-slate-300 mb-2">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="jenis_kelamin" class="block text-sm font-medium text-slate-300 mb-2">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                                <option value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="agama" class="block text-sm font-medium text-slate-300 mb-2">Agama</label>
                            <input type="text" name="agama" id="agama" value="{{ old('agama') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="anak_ke" class="block text-sm font-medium text-slate-300 mb-2">Anak Ke-</label>
                            <input type="number" name="anak_ke-" id="anak_ke" value="{{ old('anak_ke-') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                    </div>
                </div>

                <div id="step-3" class="space-y-6 hidden">
                    <h2 class="text-sm font-semibold text-slate-200">Data Orang Tua & Dokumen</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_ayah" class="block text-sm font-medium text-slate-300 mb-2">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah" value="{{ old('nama_ayah') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="nama_ibu" class="block text-sm font-medium text-slate-300 mb-2">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu" value="{{ old('nama_ibu') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="pekerjaan_ayah" class="block text-sm font-medium text-slate-300 mb-2">Pekerjaan Ayah</label>
                            <input type="text" name="pekerjaan_ayah" id="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="pekerjaan_ibu" class="block text-sm font-medium text-slate-300 mb-2">Pekerjaan Ibu</label>
                            <input type="text" name="pekerjaan_ibu" id="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat_lengkap" class="block text-sm font-medium text-slate-300 mb-2">Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">{{ old('alamat_lengkap') }}</textarea>
                        </div>
                        <div>
                            <label for="sekolah_asal" class="block text-sm font-medium text-slate-300 mb-2">Sekolah Asal</label>
                            <input type="text" name="sekolah_asal" id="sekolah_asal" value="{{ old('sekolah_asal') }}" required
                                class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all duration-300">
                        </div>
                        <div>
                            <label for="kk" class="block text-sm font-medium text-slate-300 mb-2">Kartu Keluarga (KK)</label>
                            <input type="file" name="kk" id="kk"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/20 file:text-purple-200 hover:file:bg-purple-500/30">
                        </div>
                        <div>
                            <label for="ijazah" class="block text-sm font-medium text-slate-300 mb-2">Ijazah</label>
                            <input type="file" name="ijazah" id="ijazah"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/20 file:text-purple-200 hover:file:bg-purple-500/30">
                        </div>
                        <div>
                            <label for="akta_lahir" class="block text-sm font-medium text-slate-300 mb-2">Akta Lahir</label>
                            <input type="file" name="akta_lahir" id="akta_lahir"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/20 file:text-purple-200 hover:file:bg-purple-500/30">
                        </div>
                        <div>
                            <label for="rapor" class="block text-sm font-medium text-slate-300 mb-2">Rapor</label>
                            <input type="file" name="rapor" id="rapor"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/20 file:text-purple-200 hover:file:bg-purple-500/30">
                        </div>
                        <div>
                            <label for="pas_foto" class="block text-sm font-medium text-slate-300 mb-2">Pas Foto</label>
                            <input type="file" name="pas_foto" id="pas_foto"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/20 file:text-purple-200 hover:file:bg-purple-500/30">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" id="back-btn"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-300 bg-white/[0.06] rounded-xl hover:bg-white/[0.12] transition-colors">
                        Kembali
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" id="next-btn"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 rounded-xl shadow-lg shadow-purple-500/25 transition-all">
                            Lanjut
                        </button>
                        <button type="submit" id="submit-btn"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 rounded-xl shadow-lg shadow-purple-500/25 transition-all hidden">
                            Daftar
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-white/[0.06] text-center">
                <p class="text-slate-400 text-sm">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-purple-400 hover:text-purple-300 font-medium transition-colors">Masuk</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const steps = ['step-1', 'step-2', 'step-3'];
        let currentStep = 0;

        const stepLabel = document.getElementById('step-label');
        const backBtn = document.getElementById('back-btn');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');

        function toggleStep(index) {
            steps.forEach((stepId, i) => {
                const stepEl = document.getElementById(stepId);
                const isActive = i === index;
                stepEl.classList.toggle('hidden', !isActive);
            });

            document.getElementById('step-bar-1').className = `w-20 h-1.5 rounded-full ${index >= 0 ? 'bg-purple-500/40' : 'bg-white/[0.08]'}`;
            document.getElementById('step-bar-2').className = `w-20 h-1.5 rounded-full ${index >= 1 ? 'bg-purple-500/40' : 'bg-white/[0.08]'}`;
            document.getElementById('step-bar-3').className = `w-20 h-1.5 rounded-full ${index >= 2 ? 'bg-purple-500/40' : 'bg-white/[0.08]'}`;

            stepLabel.textContent = `Langkah ${index + 1} dari 3`;
            backBtn.disabled = index === 0;
            backBtn.classList.toggle('opacity-50', index === 0);

            const isLast = index === steps.length - 1;
            nextBtn.classList.toggle('hidden', isLast);
            submitBtn.classList.toggle('hidden', !isLast);
        }

        backBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep -= 1;
                toggleStep(currentStep);
            }
        });

        nextBtn.addEventListener('click', () => {
            const activeStep = document.getElementById(steps[currentStep]);
            const inputs = Array.from(activeStep.querySelectorAll('input, select, textarea'));
            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    return;
                }
            }
            if (currentStep < steps.length - 1) {
                currentStep += 1;
                toggleStep(currentStep);
            }
        });

        toggleStep(currentStep);
    </script>

</body>
</html>
