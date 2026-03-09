<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyRegistrationSeeder extends Seeder
{
    private const KK_PATH = 'registration_docs/fBOABivnLBygIpuWiNe2ot9blyWlyIQ1cHXIEYap.jpg';
    private const IJAZAH_PATH = 'registration_docs/UYcCbFWFrBFDL6pA3KrbT3m0IM3JoIJGzvagdjmb.png';
    private const AKTA_PATH = 'registration_docs/eUmcqIdwXViUH7EQhb5BrytAH6dwubDqbE0YGcuS.jpg';
    private const STUDENT_NAMES = [
        'Ahmad Fauzan', 'Siti Aisyah', 'Muhammad Rizky', 'Nabila Putri', 'Dimas Saputra',
        'Rina Oktaviani', 'Fajar Ramadhan', 'Citra Lestari', 'Bagas Pratama', 'Intan Permata',
        'Andi Setiawan', 'Dewi Sartika', 'Rafi Maulana', 'Putri Maharani', 'Yoga Prakoso',
        'Nanda Febriani', 'Rizal Hidayat', 'Aulia Rahma', 'Bayu Kurniawan', 'Tiara Anindya',
        'Arif Nugroho', 'Laila Khairunnisa', 'Galih Wicaksono', 'Nisa Amelia', 'Iqbal Ramadhan',
        'Shinta Ayu', 'Hendra Gunawan', 'Nadia Safitri', 'Aditiya Firmansyah', 'Vina Melati',
        'Eko Prasetyo', 'Nurul Hasanah', 'Wahyu Saputro', 'Melisa Chandra', 'Agus Setiaji',
        'Fitri Handayani', 'Bima Alfarizi', 'Dinda Larasati', 'Rendy Kurnia', 'Yuni Kartika',
    ];

    private const FATHER_NAMES = [
        'Budi Santoso', 'Agus Salim', 'Joko Susilo', 'Slamet Riyadi', 'Edi Hartono',
        'Teguh Prabowo', 'Dedi Supriyadi', 'Yusuf Hidayat', 'Rudi Hermawan', 'Hendra Wijaya',
    ];

    private const MOTHER_NAMES = [
        'Siti Aminah', 'Dewi Lestari', 'Sri Wahyuni', 'Rina Marlina', 'Yuni Astuti',
        'Nur Aini', 'Indah Puspita', 'Lilis Suryani', 'Wati Kurniasih', 'Fitriani',
    ];

    public function run(): void
    {
        $statuses = ['pending', 'approved', 'rejected', 'uncertain'];
        $jurusanIds = DB::table('jurusan')->pluck('id_jurusan')->all();

        if (empty($jurusanIds)) {
            $this->command?->warn('No jurusan records found. Dummy registration seeding skipped.');
            return;
        }

        DB::transaction(function () use ($statuses, $jurusanIds) {
            $dummyUsers = DB::table('users')
                ->where('email', 'like', 'dummy.registration.%@example.test')
                ->pluck('id_user');

            if ($dummyUsers->isNotEmpty()) {
                $registrationIds = DB::table('registrasi')
                    ->whereIn('id_user', $dummyUsers)
                    ->pluck('id_registrasi');

                if ($registrationIds->isNotEmpty()) {
                    DB::table('detail_registrasi')->whereIn('id_registrasi', $registrationIds)->delete();
                }

                DB::table('user_inboxes')->whereIn('id_user', $dummyUsers)->delete();
                DB::table('registrasi')->whereIn('id_user', $dummyUsers)->delete();
                DB::table('users')->whereIn('id_user', $dummyUsers)->delete();
            }

            $counter = 1;

            foreach ($statuses as $status) {
                for ($i = 1; $i <= 10; $i++, $counter++) {
                    $studentNumber = str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
                    $createdAt = now()->subDays(41 - $counter)->setTime(8 + ($i % 8), 10 + $i, 0);
                    $fullName = self::STUDENT_NAMES[$counter - 1];
                    $emailHandle = strtolower(str_replace(' ', '.', $fullName));
                    $email = "{$emailHandle}.{$status}@example.test";
                    $userId = DB::table('users')->insertGetId([
                        'username' => strtolower(str_replace(' ', '', $fullName)) . $studentNumber,
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'role' => 1,
                        'is_verified' => true,
                        'otp_code' => null,
                        'otp_expires_at' => null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $registrasiId = DB::table('registrasi')->insertGetId([
                        'id_user' => $userId,
                        'nisn' => '2026' . str_pad((string) $counter, 6, '0', STR_PAD_LEFT),
                        'nilai_rapor' => 72 + ($counter % 23),
                        'nilai_tes' => 68 + (($counter * 3) % 29),
                        'status' => $status,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    DB::table('detail_registrasi')->insert([
                        'id_registrasi' => $registrasiId,
                        'nama_lengkap' => $fullName,
                        'nik' => '3174' . str_pad((string) (880000000000 + $counter), 12, '0', STR_PAD_LEFT),
                        'tempat_lahir' => ['Jakarta', 'Bandung', 'Bogor', 'Bekasi'][$counter % 4],
                        'tanggal_lahir' => now()->subYears(15 + ($counter % 3))->subDays($counter)->toDateString(),
                        'jenis_kelamin' => $counter % 2 === 0 ? 'Laki-laki' : 'Perempuan',
                        'agama' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'][$counter % 5],
                        'anak_ke-' => ($counter % 3) + 1,
                        'alamat_lengkap' => "Jl. Contoh Siswa No. {$counter}, Kecamatan Maju, Kota Belajar",
                        'no_hp' => '08123' . str_pad((string) (450000 + $counter), 6, '0', STR_PAD_LEFT),
                        'email' => $email,
                        'nama_ayah' => self::FATHER_NAMES[$counter % count(self::FATHER_NAMES)],
                        'nama_ibu' => self::MOTHER_NAMES[$counter % count(self::MOTHER_NAMES)],
                        'pekerjaan_ayah' => ['Wiraswasta', 'Guru', 'Karyawan Swasta', 'Petani'][$counter % 4],
                        'pekerjaan_ibu' => ['IRT', 'Guru', 'Pedagang', 'Perawat'][$counter % 4],
                        'sekolah_asal' => ['SMP Negeri 1', 'SMP Negeri 2', 'SMP Harapan Bangsa', 'MTs Cahaya Ilmu'][$counter % 4] . " {$studentNumber}",
                        'id_jurusan' => $jurusanIds[$counter % count($jurusanIds)],
                        'kk' => self::KK_PATH,
                        'ijazah' => self::IJAZAH_PATH,
                        'akta_lahir' => self::AKTA_PATH,
                    ]);

                    if ($status !== 'pending') {
                        DB::table('user_inboxes')->insert([
                            'id_user' => $userId,
                            'subject' => match ($status) {
                                'approved' => 'Registration Approved',
                                'rejected' => 'Registration Rejected',
                                'uncertain' => 'Registration Needs Review',
                            },
                            'message' => match ($status) {
                                'approved' => "Hello {$fullName}, your registration with NISN 2026" . str_pad((string) $counter, 6, '0', STR_PAD_LEFT) . " has been approved by the school.",
                                'rejected' => "Hello {$fullName}, your registration with NISN 2026" . str_pad((string) $counter, 6, '0', STR_PAD_LEFT) . " has been rejected by the school.",
                                'uncertain' => "Hello {$fullName}, your registration with NISN 2026" . str_pad((string) $counter, 6, '0', STR_PAD_LEFT) . " is still under additional review by the school.",
                            },
                            'status' => $status,
                            'read_at' => null,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                        ]);
                    }
                }
            }
        });
    }
}
