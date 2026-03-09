<?php

namespace App\Http\Controllers;

use App\Models\AdmissionTest;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    public function showTest(string $token)
    {
        $test = AdmissionTest::with('registration')->where('token', $token)->firstOrFail();
        abort_unless($test->registration && $test->registration->id_user === Auth::id(), 403);
        abort_unless($test->registration->status === 'approved', 403);

        return view('user.test', [
            'test' => $test,
            'questionSets' => $this->questionSets($test->test_type),
        ]);
    }

    public function submitTest(Request $request, string $token)
    {
        $test = AdmissionTest::with('registration')->where('token', $token)->firstOrFail();
        abort_unless($test->registration && $test->registration->id_user === Auth::id(), 403);
        abort_unless($test->status === 'assigned', 403, 'Tes ini sudah dikerjakan.');

        $questionSets = $this->questionSets($test->test_type);
        $validation = [];

        foreach (array_keys($questionSets['basic']) as $key) {
            $validation["basic_answers.{$key}"] = ['required', 'in:A,B,C,D'];
        }
        foreach (array_keys($questionSets['interest']) as $key) {
            $validation["interest_answers.{$key}"] = ['required', 'in:A,B,C,D'];
        }

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
        ]);

        return redirect()->route('user.inbox')->with('success', 'Tes berhasil dikirim. Silakan tunggu hasil seleksi dari sekolah.');
    }

    public function showReRegistration(string $token)
    {
        $registration = DB::table('registrasi as registrasi')
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->where('registrasi.re_registration_token', $token)
            ->where('registrasi.selection_status', 'passed')
            ->select(
                'registrasi.id_registrasi',
                'registrasi.id_user',
                'registrasi.nisn',
                'detail.nama_lengkap',
                'detail.email',
                'detail.no_hp',
                'detail.alamat_lengkap',
                'detail.sekolah_asal',
                'detail.id_jurusan'
            )
            ->firstOrFail();

        abort_unless((int) $registration->id_user === (int) Auth::id(), 403);

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
            ->where('registrasi.selection_status', 'passed')
            ->select(
                'registrasi.id_registrasi',
                'registrasi.id_user',
                'registrasi.nisn',
                'detail.sekolah_asal'
            )
            ->firstOrFail();

        abort_unless((int) $registration->id_user === (int) Auth::id(), 403);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'no_hp' => ['required', 'string', 'max:50'],
            'alamat' => ['required', 'string'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
        ]);

        abort_if(Siswa::where('id_registrasi', $registration->id_registrasi)->exists(), 403, 'Daftar ulang sudah dilakukan.');

        Siswa::create([
            'nis' => $this->generateNis(),
            'id_registrasi' => $registration->id_registrasi,
            'id_user' => $registration->id_user,
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'asal_sekolah' => $registration->sekolah_asal,
            'id_jurusan' => $validated['id_jurusan'],
            'tanggal_daftar_ulang' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('registrasi')
            ->where('id_registrasi', $registration->id_registrasi)
            ->update([
                'current_stage' => 'enrolled',
                're_registration_token' => null,
            ]);

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
