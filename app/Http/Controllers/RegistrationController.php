<?php

namespace App\Http\Controllers;

use App\Models\Registrasi;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\AdminService;

class RegistrationController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        return view('user.form', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jurusan' => 'required|exists:jurusan,id_jurusan',
            'nisn' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|max:50',
            'nik' => 'required|string|max:50',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string|max:50',
            'agama' => 'required|string|max:50',
            'anak_ke-' => 'required|integer',
            'alamat_lengkap' => 'required|string|max:255',
            'nama_ayah' => 'required|string|max:50',
            'nama_ibu' => 'required|string|max:50',
            'pekerjaan_ayah' => 'required|string|max:50',
            'pekerjaan_ibu' => 'required|string|max:50',
            'sekolah_asal' => 'required|string|max:50',
            'nilai_rapor' => 'required|integer',
            'no_hp' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'kk' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'akta_lahir' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $files = ['kk', 'ijazah', 'akta_lahir'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $path = $request->file($file)->store('registration_docs', 'public');
                $validated[$file] = $path;
            }
        }

        DB::transaction(function () use ($validated) {
            $registrasiData = [
                'id_user' => Auth::id(),
                'nisn' => $validated['nisn'],
                'nilai_rapor' => $validated['nilai_rapor'],
                'nilai_tes' => 0,
                'status' => 'pending',
            ];

            if (Schema::hasColumn('registrasi', 'nama_lengkap')) {
                $registrasiData['nama_lengkap'] = $validated['nama_lengkap'];
            }

            $registrasi = Registrasi::create($registrasiData);

            $detailData = [
                'id_registrasi' => $registrasi->id_registrasi,
                'nik' => $validated['nik'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'],
                'anak_ke-' => $validated['anak_ke-'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'no_hp' => $validated['no_hp'],
                'email' => $validated['email'],
                'nama_ayah' => $validated['nama_ayah'],
                'nama_ibu' => $validated['nama_ibu'],
                'pekerjaan_ayah' => $validated['pekerjaan_ayah'],
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
                'sekolah_asal' => $validated['sekolah_asal'],
                'id_jurusan' => $validated['id_jurusan'],
                'kk' => $validated['kk'] ?? '',
                'ijazah' => $validated['ijazah'] ?? '',
                'akta_lahir' => $validated['akta_lahir'] ?? '',
            ];

            if (Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
                $detailData['nama_lengkap'] = $validated['nama_lengkap'];
            }

            DB::table('detail_registrasi')->insert($detailData);
        });

        $this->adminService->logActivity(Auth::id(), 'Submitted new registration for: ' . $validated['nama_lengkap'], $request->ip());

        return redirect()->route('index')->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari admin.');
    }
}
