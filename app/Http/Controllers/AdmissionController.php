<?php

namespace App\Http\Controllers;

use App\Models\AdmissionTest;
use App\Models\Registrasi;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use App\Mail\ReRegistrationConfirmedMail;

class AdmissionController extends Controller
{
    public function showTest(string $token)
    {
        $test = AdmissionTest::with('registration')->where('token', $token)->firstOrFail();
        abort_unless($test->registration && $test->registration->id_user === Auth::id(), 403);
        abort_unless($test->registration->status === 'approved', 403);
        $alreadySubmitted = AdmissionTest::where('id_registrasi', $test->registration->id_registrasi)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->exists();
        if ($alreadySubmitted && $test->status !== 'assigned') {
            return redirect()->route('index')->with('warning', 'Tes sudah dikerjakan. Silakan tunggu hasil seleksi.');
        }

        return view('user.test', [
            'test' => $test,
            'questionSets' => $this->questionSets($test->test_type),
            'jurusans' => DB::table('jurusan')->get(),
        ]);
    }

    public function showCurrentTest()
    {
        $user = Auth::user();
        abort_unless((int) $user->role === 1, 403);

        if (Schema::hasColumn('registrasi', 'id_user')) {
            $registrasi = Registrasi::where('id_user', $user->id_user)->orderByDesc('id_registrasi')->firstOrFail();
        } else {
            $registrasi = Registrasi::where('email', $user->email)->orderByDesc('id_registrasi')->firstOrFail();
        }
        abort_unless($registrasi->status === 'approved', 403);

        if (in_array($registrasi->selection_status, ['passed', 'uncertain'], true) && !empty($registrasi->re_registration_token)) {
            return redirect()->route('user.reregister.show', $registrasi->re_registration_token);
        }
        if ($registrasi->selection_status === 'failed' || $registrasi->current_stage === 'enrolled') {
            return redirect()->route('index');
        }

        $alreadySubmitted = AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->exists();
        if ($alreadySubmitted || $registrasi->current_stage === 'test_submitted') {
            return redirect()->route('index')->with('warning', 'Tes sudah dikerjakan. Silakan tunggu hasil seleksi.');
        }

        $test = AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->whereIn('status', ['assigned', 'submitted'])
            ->orderByDesc('id_admission_test')
            ->first();

        if (!$test) {
            $token = \Illuminate\Support\Str::uuid()->toString();
            $test = AdmissionTest::create([
                'id_registrasi' => $registrasi->id_registrasi,
                'token' => $token,
                'test_type' => 'primary',
                'status' => 'assigned',
            ]);
            $registrasi->update([
                'current_stage' => 'test_assigned',
                'test_access_token' => $token,
            ]);
        }

        return view('user.test', [
            'test' => $test,
            'questionSets' => $this->questionSets($test->test_type),
            'jurusans' => DB::table('jurusan')->get(),
        ]);
    }

    public function submitTest(Request $request, string $token)
    {
        $test = AdmissionTest::with('registration')->where('token', $token)->firstOrFail();
        abort_unless($test->registration && $test->registration->id_user === Auth::id(), 403);
        abort_unless($test->status === 'assigned', 403, 'Tes ini sudah dikerjakan.');
        $alreadySubmitted = AdmissionTest::where('id_registrasi', $test->registration->id_registrasi)
            ->whereIn('status', ['submitted', 'reviewed'])
            ->exists();
        abort_unless(!$alreadySubmitted, 403, 'Tes ini sudah dikerjakan.');

        $questionSets = $this->questionSets($test->test_type);
        $validation = [];

        foreach (array_keys($questionSets['basic']) as $key) {
            $validation["basic_answers.{$key}"] = ['required', 'in:A,B,C,D'];
        }
        foreach (array_keys($questionSets['interest']) as $key) {
            $validation["interest_answers.{$key}"] = ['required', 'in:A,B,C,D'];
        }
        $validation['id_jurusan'] = ['required', 'exists:jurusan,id_jurusan'];

        $validated = $request->validate($validation);

        $basicCorrect = 0;
        foreach ($questionSets['basic'] as $key => $question) {
            if (($validated['basic_answers'][$key] ?? null) === $question['correct']) {
                $basicCorrect++;
            }
        }

        $interestCorrect = 0;
        foreach ($questionSets['interest'] as $key => $question) {
            if (($validated['interest_answers'][$key] ?? null) === $question['correct']) {
                $interestCorrect++;
            }
        }

        $basicScore = (int) round(($basicCorrect / count($questionSets['basic'])) * 100);
        $interestScore = (int) round(($interestCorrect / count($questionSets['interest'])) * 100);
        $totalScore = (int) round(($basicScore + $interestScore) / 2);

        $test->update([
            'id_jurusan' => $validated['id_jurusan'],
            'answers' => [
                'basic' => $validated['basic_answers'],
                'interest' => $validated['interest_answers'],
            ],
            'basic_score' => $basicScore,
            'interest_score' => $interestScore,
            'total_score' => $totalScore,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $test->registration->update([
            'current_stage' => 'test_submitted',
            'id_jurusan' => $validated['id_jurusan'],
        ]);

        DB::table('detail_registrasi')
            ->where('id_registrasi', $test->registration->id_registrasi)
            ->update(['id_jurusan' => $validated['id_jurusan']]);

        return redirect()->route('user.inbox')->with('success', 'Tes berhasil dikirim. Silakan tunggu hasil seleksi dari sekolah.');
    }

    public function showReRegistration(string $token)
    {
        $registration = DB::table('registrasi as registrasi')
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->leftJoin('users as users', 'users.id_user', '=', 'registrasi.id_user')
            ->where('registrasi.re_registration_token', $token)
            ->whereIn('registrasi.selection_status', ['passed', 'uncertain'])
            ->select(
                'registrasi.id_registrasi',
                'registrasi.id_user',
                'registrasi.nisn',
                'registrasi.nilai_rapor',
                'detail.nama_lengkap',
                'detail.nik',
                'detail.tempat_lahir',
                'detail.tanggal_lahir',
                'detail.jenis_kelamin',
                'detail.agama',
                DB::raw('detail.`anak_ke-` as anak_ke'),
                'detail.email',
                'detail.no_hp',
                'detail.alamat_lengkap',
                'detail.nama_ayah',
                'detail.nama_ibu',
                'detail.pekerjaan_ayah',
                'detail.pekerjaan_ibu',
                'detail.sekolah_asal',
                'detail.id_jurusan',
                'users.nama_lengkap as user_nama_lengkap',
                'users.email as user_email',
                'users.no_hp as user_no_hp'
            )
            ->firstOrFail();

        $authUser = Auth::user();
        $registrationEmail = $registration->email ?? $registration->user_email ?? null;
        $allowed = ((int) $registration->id_user === (int) $authUser->id_user)
            || (empty($registration->id_user) && $registrationEmail && strcasecmp($registrationEmail, $authUser->email) === 0);
        abort_unless($allowed, 403);

        return view('user.reregister', [
            'registration' => $registration,
            'token' => $token,
            'jurusans' => DB::table('jurusan')->get(),
        ]);
    }

    public function submitReRegistration(Request $request, string $token)
    {
        $registration = DB::table('registrasi as registrasi')
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->where('registrasi.re_registration_token', $token)
            ->whereIn('registrasi.selection_status', ['passed', 'uncertain'])
            ->select(
                'registrasi.id_registrasi',
                'registrasi.id_user',
                'registrasi.nisn',
                'detail.sekolah_asal',
                'detail.email'
            )
            ->firstOrFail();

        $authUser = Auth::user();
        $registrationEmail = $registration->email ?? null;
        $allowed = ((int) $registration->id_user === (int) $authUser->id_user)
            || (empty($registration->id_user) && $registrationEmail && strcasecmp($registrationEmail, $authUser->email) === 0);
        abort_unless($allowed, 403);

        $validated = $request->validate([
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
            'nisn' => ['required', 'string', 'max:50'],
            'nama_lengkap' => ['required', 'string', 'max:50'],
            'nik' => ['required', 'string', 'max:50'],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'string', 'max:50'],
            'agama' => ['required', 'string', 'max:50'],
            'anak_ke-' => ['required', 'integer'],
            'alamat' => ['required', 'string'],
            'nama_ayah' => ['required', 'string', 'max:50'],
            'nama_ibu' => ['required', 'string', 'max:50'],
            'pekerjaan_ayah' => ['required', 'string', 'max:50'],
            'pekerjaan_ibu' => ['required', 'string', 'max:50'],
            'sekolah_asal' => ['required', 'string', 'max:50'],
            'nilai_rapor' => ['required', 'integer'],
            'no_hp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (Siswa::where('id_registrasi', $registration->id_registrasi)->exists()) {
            return redirect()->route('index')->with('warning', 'Daftar ulang sudah dilakukan.');
        }

        $siswa = Siswa::create([
            'nis' => $this->generateNis(),
            'id_registrasi' => $registration->id_registrasi,
            'id_user' => $registration->id_user,
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'asal_sekolah' => $validated['sekolah_asal'],
            'id_jurusan' => $validated['id_jurusan'],
            'tanggal_daftar_ulang' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $registrasiUpdate = [];
        $registrasiColumns = Schema::hasTable('registrasi') ? Schema::getColumnListing('registrasi') : [];
        $registrasiMap = [
            'nisn' => 'nisn',
            'nilai_rapor' => 'nilai_rapor',
            'id_jurusan' => 'id_jurusan',
            'nama_lengkap' => 'nama_lengkap',
            'nik' => 'nik',
            'tempat_lahir' => 'tempat_lahir',
            'tanggal_lahir' => 'tanggal_lahir',
            'jenis_kelamin' => 'jenis_kelamin',
            'agama' => 'agama',
            'anak_ke-' => 'anak_ke-',
            'alamat_lengkap' => 'alamat',
            'nama_ayah' => 'nama_ayah',
            'nama_ibu' => 'nama_ibu',
            'pekerjaan_ayah' => 'pekerjaan_ayah',
            'pekerjaan_ibu' => 'pekerjaan_ibu',
            'sekolah_asal' => 'sekolah_asal',
            'no_hp' => 'no_hp',
            'email' => 'email',
        ];
        foreach ($registrasiMap as $column => $key) {
            if (in_array($column, $registrasiColumns, true)) {
                $registrasiUpdate[$column] = $validated[$key];
            }
        }
        if (!empty($registrasiUpdate)) {
            DB::table('registrasi')
                ->where('id_registrasi', $registration->id_registrasi)
                ->update($registrasiUpdate);
        }

        if (Schema::hasTable('detail_registrasi')) {
            $detailColumns = Schema::getColumnListing('detail_registrasi');
            $detailMap = [
                'nama_lengkap' => 'nama_lengkap',
                'nik' => 'nik',
                'tempat_lahir' => 'tempat_lahir',
                'tanggal_lahir' => 'tanggal_lahir',
                'jenis_kelamin' => 'jenis_kelamin',
                'agama' => 'agama',
                'anak_ke-' => 'anak_ke-',
                'alamat_lengkap' => 'alamat',
                'no_hp' => 'no_hp',
                'email' => 'email',
                'nama_ayah' => 'nama_ayah',
                'nama_ibu' => 'nama_ibu',
                'pekerjaan_ayah' => 'pekerjaan_ayah',
                'pekerjaan_ibu' => 'pekerjaan_ibu',
                'sekolah_asal' => 'sekolah_asal',
                'id_jurusan' => 'id_jurusan',
            ];
            $detailUpdate = [];
            foreach ($detailMap as $column => $key) {
                if (in_array($column, $detailColumns, true)) {
                    $detailUpdate[$column] = $validated[$key];
                }
            }
            if (!empty($detailUpdate)) {
                DB::table('detail_registrasi')
                    ->where('id_registrasi', $registration->id_registrasi)
                    ->update($detailUpdate);
            }
        }

        DB::table('registrasi')
            ->where('id_registrasi', $registration->id_registrasi)
            ->update([
                'current_stage' => 'enrolled',
                're_registration_token' => null,
            ]);

        User::where('id_user', $registration->id_user)->update([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
        ]);

        $user = User::find($registration->id_user);
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new ReRegistrationConfirmedMail($user, $siswa->nis));
        }

        return redirect()->route('index')->with('success', 'Daftar ulang berhasil. Data Anda sudah masuk ke tabel siswa.');
    }

    private function generateNis(): string
    {
        $yearPrefix = (int) now()->format('y');
        $rangeStart = $yearPrefix * 10000;
        $rangeEnd = (($yearPrefix + 1) * 10000) - 1;

        $lastNis = (int) (Siswa::whereBetween('nis', [$rangeStart, $rangeEnd])->max('nis') ?? $rangeStart);

        return (string) max($rangeStart + 1, $lastNis + 1);
    }

    private function questionSets(string $type): array
    {
        if ($type === 'follow_up') {
            return [
                'basic' => [
                    ['question' => 'Hasil dari 18 : 3 adalah ...', 'options' => ['A' => '6', 'B' => '7', 'C' => '8', 'D' => '9'], 'correct' => 'A'],
                    ['question' => 'Sinonim kata "cermat" adalah ...', 'options' => ['A' => 'lambat', 'B' => 'teliti', 'C' => 'kasar', 'D' => 'acuh'], 'correct' => 'B'],
                    ['question' => 'Jika 5 buku seharga 40.000, maka 1 buku seharga ...', 'options' => ['A' => '6.000', 'B' => '7.000', 'C' => '8.000', 'D' => '9.000'], 'correct' => 'C'],
                    ['question' => 'Planet tempat manusia tinggal adalah ...', 'options' => ['A' => 'Mars', 'B' => 'Venus', 'C' => 'Jupiter', 'D' => 'Bumi'], 'correct' => 'D'],
                    ['question' => 'Urutan yang benar adalah ...', 'options' => ['A' => 'Pagi-Siang-Malam', 'B' => 'Malam-Pagi-Siang', 'C' => 'Siang-Pagi-Malam', 'D' => 'Malam-Siang-Pagi'], 'correct' => 'A'],
                ],
                'interest' => [
                    ['question' => 'Kegiatan yang paling Anda sukai adalah ...', 'options' => ['A' => 'Menganalisis masalah', 'B' => 'Menghafal cepat', 'C' => 'Menghindari tugas', 'D' => 'Menunda pekerjaan'], 'correct' => 'A'],
                    ['question' => 'Saat bekerja dalam tim, Anda cenderung ...', 'options' => ['A' => 'Diam saja', 'B' => 'Memberi ide', 'C' => 'Menunggu semua selesai', 'D' => 'Keluar dari diskusi'], 'correct' => 'B'],
                    ['question' => 'Bidang yang ingin Anda dalami adalah ...', 'options' => ['A' => 'Teknologi/keahlian', 'B' => 'Tidak tahu sama sekali', 'C' => 'Asal ikut teman', 'D' => 'Yang paling mudah'], 'correct' => 'A'],
                    ['question' => 'Jika menemui kesulitan, Anda akan ...', 'options' => ['A' => 'Mencari solusi', 'B' => 'Menyerah', 'C' => 'Menyalahkan orang lain', 'D' => 'Menghindar'], 'correct' => 'A'],
                    ['question' => 'Pilihan yang paling menggambarkan Anda adalah ...', 'options' => ['A' => 'Penasaran dan mau belajar', 'B' => 'Cepat bosan', 'C' => 'Tidak suka mencoba', 'D' => 'Acuh'], 'correct' => 'A'],
                ],
            ];
        }

        return [
            'basic' => [
                ['question' => 'Hasil dari 12 + 8 adalah ...', 'options' => ['A' => '18', 'B' => '19', 'C' => '20', 'D' => '21'], 'correct' => 'C'],
                ['question' => 'Ibu kota Indonesia adalah ...', 'options' => ['A' => 'Bandung', 'B' => 'Jakarta', 'C' => 'Surabaya', 'D' => 'Medan'], 'correct' => 'B'],
                ['question' => 'Antonim kata "rajin" adalah ...', 'options' => ['A' => 'tekun', 'B' => 'ulet', 'C' => 'malas', 'D' => 'aktif'], 'correct' => 'C'],
                ['question' => '2, 4, 6, 8, ...', 'options' => ['A' => '9', 'B' => '10', 'C' => '11', 'D' => '12'], 'correct' => 'B'],
                ['question' => 'Makhluk hidup membutuhkan ... untuk bernapas', 'options' => ['A' => 'udara', 'B' => 'tanah', 'C' => 'batu', 'D' => 'kayu'], 'correct' => 'A'],
            ],
            'interest' => [
                ['question' => 'Saat ada tugas baru, Anda biasanya ...', 'options' => ['A' => 'Antusias mencoba', 'B' => 'Menunggu orang lain', 'C' => 'Menghindar', 'D' => 'Tidak peduli'], 'correct' => 'A'],
                ['question' => 'Kegiatan yang paling cocok untuk Anda adalah ...', 'options' => ['A' => 'Belajar hal baru', 'B' => 'Tidur seharian', 'C' => 'Tidak ikut kegiatan', 'D' => 'Melewatkan kelas'], 'correct' => 'A'],
                ['question' => 'Jika diminta presentasi, Anda akan ...', 'options' => ['A' => 'Menyiapkan materi', 'B' => 'Menolak langsung', 'C' => 'Kabur', 'D' => 'Diam tanpa usaha'], 'correct' => 'A'],
                ['question' => 'Anda lebih suka lingkungan belajar yang ...', 'options' => ['A' => 'Mendorong berkembang', 'B' => 'Bebas tanpa tujuan', 'C' => 'Asal ramai', 'D' => 'Tidak teratur'], 'correct' => 'A'],
                ['question' => 'Ketika gagal, Anda biasanya ...', 'options' => ['A' => 'Mencoba lagi', 'B' => 'Menyalahkan keadaan', 'C' => 'Berhenti total', 'D' => 'Menghindar'], 'correct' => 'A'],
            ],
        ];
    }
}
