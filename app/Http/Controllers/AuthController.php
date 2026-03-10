<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        $isOnline = $this->authService->isOnline();
        $captcha = $this->authService->generateCaptcha();
        session([
            'captcha_answer' => $captcha['answer'],
            'is_online_captcha' => $isOnline
        ]);
        
        return view('auth.login', [
            'captchaQuestion' => $captcha['question'],
            'isOnline' => $isOnline
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $files = ['kk', 'ijazah', 'akta_lahir', 'rapor', 'pas_foto'];
        foreach ($files as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('registration_docs', 'public');
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $registrasiData = [
                'nisn' => $data['nisn'],
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nilai_rapor')) {
                $registrasiData['nilai_rapor'] = (int) ($data['nilai_rapor'] ?? 0);
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nilai_tes')) {
                $registrasiData['nilai_tes'] = 0;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'status')) {
                $registrasiData['status'] = 'pending';
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nama_lengkap')) {
                $registrasiData['nama_lengkap'] = $data['nama_lengkap'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nik')) {
                $registrasiData['nik'] = $data['nik'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'tempat_lahir')) {
                $registrasiData['tempat_lahir'] = $data['tempat_lahir'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'tanggal_lahir')) {
                $registrasiData['tanggal_lahir'] = $data['tanggal_lahir'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'jenis_kelamin')) {
                $registrasiData['jenis_kelamin'] = $data['jenis_kelamin'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'agama')) {
                $registrasiData['agama'] = $data['agama'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'anak_ke-')) {
                $registrasiData['anak_ke-'] = $data['anak_ke-'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'alamat_lengkap')) {
                $registrasiData['alamat_lengkap'] = $data['alamat_lengkap'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nama_ayah')) {
                $registrasiData['nama_ayah'] = $data['nama_ayah'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'nama_ibu')) {
                $registrasiData['nama_ibu'] = $data['nama_ibu'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'pekerjaan_ayah')) {
                $registrasiData['pekerjaan_ayah'] = $data['pekerjaan_ayah'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'pekerjaan_ibu')) {
                $registrasiData['pekerjaan_ibu'] = $data['pekerjaan_ibu'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'sekolah_asal')) {
                $registrasiData['sekolah_asal'] = $data['sekolah_asal'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'no_hp')) {
                $registrasiData['no_hp'] = $data['no_hp'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'email')) {
                $registrasiData['email'] = $data['email'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'id_jurusan') && isset($data['id_jurusan'])) {
                $registrasiData['id_jurusan'] = $data['id_jurusan'];
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'kk')) {
                $registrasiData['kk'] = $data['kk'] ?? null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'ijazah')) {
                $registrasiData['ijazah'] = $data['ijazah'] ?? null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'akta_lahir')) {
                $registrasiData['akta_lahir'] = $data['akta_lahir'] ?? null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'rapor')) {
                $registrasiData['rapor'] = $data['rapor'] ?? null;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'pas_foto')) {
                $registrasiData['pas_foto'] = $data['pas_foto'] ?? null;
            }

            $registrasi = new \App\Models\Registrasi();
            $registrasi->forceFill($registrasiData);
            $registrasi->save();

            if (\Illuminate\Support\Facades\Schema::hasTable('detail_registrasi')) {
                $detailData = [
                    'id_registrasi' => $registrasi->id_registrasi,
                    'nik' => $data['nik'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'agama' => $data['agama'],
                    'anak_ke-' => $data['anak_ke-'],
                    'alamat_lengkap' => $data['alamat_lengkap'],
                    'no_hp' => $data['no_hp'],
                    'email' => $data['email'],
                    'nama_ayah' => $data['nama_ayah'],
                    'nama_ibu' => $data['nama_ibu'],
                    'pekerjaan_ayah' => $data['pekerjaan_ayah'],
                    'pekerjaan_ibu' => $data['pekerjaan_ibu'],
                    'sekolah_asal' => $data['sekolah_asal'],
                    'id_jurusan' => $data['id_jurusan'] ?? null,
                    'kk' => $data['kk'] ?? '',
                    'ijazah' => $data['ijazah'] ?? '',
                    'akta_lahir' => $data['akta_lahir'] ?? '',
                    'rapor' => $data['rapor'] ?? '',
                    'pas_foto' => $data['pas_foto'] ?? '',
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
                    $detailData['nama_lengkap'] = $data['nama_lengkap'];
                }

                \Illuminate\Support\Facades\DB::table('detail_registrasi')->insert($detailData);
            }
        });

        return redirect()->route('login')->with('success', 'Registrasi berhasil. Silakan tunggu persetujuan admin untuk mendapatkan akun.');
    }

    public function login(Request $request)
    {
        $isOnline = session('is_online_captcha', false);

        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];

        if ($isOnline) {
            $rules['g-recaptcha-response'] = ['required'];
        } else {
            $rules['captcha'] = ['required', 'integer'];
        }

        $request->validate($rules);

        if ($isOnline) {
            if (!$this->authService->verifyRecaptcha($request->input('g-recaptcha-response'))) {
                return back()->withErrors(['captcha' => 'reCAPTCHA verification failed.'])->withInput();
            }
        } else {
            $captchaAnswer = session('captcha_answer');
            if ((int) $request->captcha !== (int) $captchaAnswer) {
                return back()->withErrors(['captcha' => 'Incorrect CAPTCHA answer.'])->withInput();
            }
        }

        $user = $this->authService->attemptLogin($request->email, $request->password);

        if ($user === false) {
            return back()->withErrors(['email' => 'Invalid credentials, token, or account not approved.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ((int) $user->role === 1) {
            $registrasiQuery = \App\Models\Registrasi::query();
            if (\Illuminate\Support\Facades\Schema::hasColumn('registrasi', 'id_user')) {
                $registrasiQuery->where('id_user', $user->id_user);
            } else {
                $registrasiQuery->where('email', $user->email);
            }
            $registrasi = $registrasiQuery->orderByDesc('id_registrasi')->first();

            if ($registrasi) {
                if (in_array($registrasi->selection_status, ['passed', 'uncertain'], true) && !empty($registrasi->re_registration_token)) {
                    return redirect()->route('user.reregister.show', $registrasi->re_registration_token);
                }

                if ($registrasi->current_stage === 'enrolled' || $registrasi->selection_status === 'failed') {
                    return redirect()->intended('/index');
                }
            }

            return redirect()->route('user.test.current');
        }
        return redirect()->intended('/index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
