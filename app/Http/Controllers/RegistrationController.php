<?php

namespace App\Http\Controllers;

use App\Models\Registrasi;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $validated['id_user'] = Auth::id();
        Registrasi::create($validated);

        $this->adminService->logActivity(Auth::id(), 'Submitted new registration for: ' . $validated['nama_lengkap'], $request->ip());

        return redirect()->route('index')->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari admin.');
    }
}
