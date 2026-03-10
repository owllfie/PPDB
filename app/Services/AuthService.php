<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AuthService
{
    public function registerUser(array $data): User
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = DB::transaction(function () use ($data, $otp) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'password' => Hash::make($data['password'], ['rounds' => 12]),
                'role' => 1,
                'is_verified' => false,
                'otp_code' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            if (Schema::hasTable('registrasi')) {
                $registrasiData = [];

                if (Schema::hasColumn('registrasi', 'nisn')) {
                    $registrasiData['nisn'] = $data['nisn'];
                }
                if (Schema::hasColumn('registrasi', 'nilai_rapor')) {
                    $registrasiData['nilai_rapor'] = $data['nilai_rapor'] ?? 0;
                }
                if (Schema::hasColumn('registrasi', 'nilai_tes')) {
                    $registrasiData['nilai_tes'] = 0;
                }
                if (Schema::hasColumn('registrasi', 'status')) {
                    $registrasiData['status'] = 'pending';
                }

                if (Schema::hasColumn('registrasi', 'id_user')) {
                    $registrasiData['id_user'] = $user->id_user;
                }
                if (Schema::hasColumn('registrasi', 'nama_lengkap')) {
                    $registrasiData['nama_lengkap'] = $data['nama_lengkap'];
                }
                if (Schema::hasColumn('registrasi', 'nik')) {
                    $registrasiData['nik'] = $data['nik'];
                }
                if (Schema::hasColumn('registrasi', 'tempat_lahir')) {
                    $registrasiData['tempat_lahir'] = $data['tempat_lahir'];
                }
                if (Schema::hasColumn('registrasi', 'tanggal_lahir')) {
                    $registrasiData['tanggal_lahir'] = $data['tanggal_lahir'];
                }
                if (Schema::hasColumn('registrasi', 'jenis_kelamin')) {
                    $registrasiData['jenis_kelamin'] = $data['jenis_kelamin'];
                }
                if (Schema::hasColumn('registrasi', 'agama')) {
                    $registrasiData['agama'] = $data['agama'];
                }
                if (Schema::hasColumn('registrasi', 'anak_ke-')) {
                    $registrasiData['anak_ke-'] = $data['anak_ke-'];
                }
                if (Schema::hasColumn('registrasi', 'alamat_lengkap')) {
                    $registrasiData['alamat_lengkap'] = $data['alamat_lengkap'];
                }
                if (Schema::hasColumn('registrasi', 'nama_ayah')) {
                    $registrasiData['nama_ayah'] = $data['nama_ayah'];
                }
                if (Schema::hasColumn('registrasi', 'nama_ibu')) {
                    $registrasiData['nama_ibu'] = $data['nama_ibu'];
                }
                if (Schema::hasColumn('registrasi', 'pekerjaan_ayah')) {
                    $registrasiData['pekerjaan_ayah'] = $data['pekerjaan_ayah'];
                }
                if (Schema::hasColumn('registrasi', 'pekerjaan_ibu')) {
                    $registrasiData['pekerjaan_ibu'] = $data['pekerjaan_ibu'];
                }
                if (Schema::hasColumn('registrasi', 'sekolah_asal')) {
                    $registrasiData['sekolah_asal'] = $data['sekolah_asal'];
                }
                if (Schema::hasColumn('registrasi', 'no_hp')) {
                    $registrasiData['no_hp'] = $data['no_hp'];
                }
                if (Schema::hasColumn('registrasi', 'email')) {
                    $registrasiData['email'] = $data['email'];
                }

                foreach (['kk', 'ijazah', 'akta_lahir', 'rapor', 'pas_foto'] as $docField) {
                    if (Schema::hasColumn('registrasi', $docField)) {
                        $registrasiData[$docField] = $data[$docField] ?? null;
                    }
                }

                if (empty($registrasiData)) {
                    return $user;
                }

                if (Schema::hasColumn('registrasi', 'created_at')) {
                    $registrasiData['created_at'] = Carbon::now();
                }
                if (Schema::hasColumn('registrasi', 'updated_at')) {
                    $registrasiData['updated_at'] = Carbon::now();
                }

                $registrasiId = DB::table('registrasi')->insertGetId($registrasiData);

                if (Schema::hasTable('detail_registrasi')) {
                    $detailData = [
                        'id_registrasi' => $registrasiId,
                    ];

                    $detailFields = [
                        'nik',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'jenis_kelamin',
                        'agama',
                        'anak_ke-',
                        'alamat_lengkap',
                        'no_hp',
                        'email',
                        'nama_ayah',
                        'nama_ibu',
                        'pekerjaan_ayah',
                        'pekerjaan_ibu',
                        'sekolah_asal',
                    ];

                    foreach ($detailFields as $field) {
                        if (Schema::hasColumn('detail_registrasi', $field)) {
                            $detailData[$field] = $data[$field];
                        }
                    }

                    if (Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
                        $detailData['nama_lengkap'] = $data['nama_lengkap'];
                    }

                    foreach (['kk', 'ijazah', 'akta_lahir', 'rapor', 'pas_foto'] as $docField) {
                        if (Schema::hasColumn('detail_registrasi', $docField)) {
                            $detailData[$docField] = $data[$docField] ?? null;
                        }
                    }

                    DB::table('detail_registrasi')->insert($detailData);
                }
            }

            return $user;
        });

        Mail::to($user->email)->queue(new VerificationEmail($user, $otp));

        return $user;
    }

    public function verifyOtp(User $user, string $otp): bool
    {
        if ($user->is_verified) {
            return true;
        }

        if ($user->otp_code !== $otp) {
            return false;
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return false;
        }

        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }

    public function resendOtp(User $user): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->queue(new VerificationEmail($user, $otp));
    }

    public function attemptLogin(string $email, string $password): User|false
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        if (!$user->is_verified) {
            return false;
        }

        return $user;
    }

    public function generateCaptcha(): array
    {
        $num1 = random_int(1, 20);
        $num2 = random_int(1, 20);
        $operators = ['+', '-'];
        $operator = $operators[array_rand($operators)];

        if ($operator === '-' && $num1 < $num2) {
            [$num1, $num2] = [$num2, $num1];
        }

        $answer = $operator === '+' ? $num1 + $num2 : $num1 - $num2;
        $question = "{$num1} {$operator} {$num2} = ?";

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    public function isOnline(): bool
    {
        $connected = @fsockopen("www.google.com", 443);
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }

    public function verifyRecaptcha(string $token): bool
    {
        $secret = env('RECAPTCHA_SECRET_KEY');
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$token}");
        $responseKeys = json_decode($response, true);
        return (bool)($responseKeys["success"] ?? false);
    }
}
