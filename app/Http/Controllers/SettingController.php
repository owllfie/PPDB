<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AdminService;

class SettingController extends Controller
{
    protected SettingService $settingService;
    protected AdminService $adminService;

    public function __construct(SettingService $settingService, AdminService $adminService)
    {
        $this->settingService = $settingService;
        $this->adminService = $adminService;
    }

    public function index()
    {
        $setting = $this->settingService->getSettings();
        return view('admin.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'kepala_sekolah' => ['nullable', 'string', 'max:50'],
            'nomor_kontak' => ['nullable', 'string', 'max:50'],
            'logo_sekolah' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->settingService->updateSettings($request->all(), Auth::user()->id_user);
        $this->adminService->logActivity(Auth::user()->id_user, 'Updated web settings', $request->ip());

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
