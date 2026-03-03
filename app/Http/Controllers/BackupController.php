<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    protected BackupService $backupService;
    protected AdminService $adminService;

    public function __construct(BackupService $backupService, AdminService $adminService)
    {
        $this->backupService = $backupService;
        $this->adminService = $adminService;
    }

    public function download(Request $request)
    {
        $this->adminService->logActivity(Auth::user()->id_user, 'Downloaded database backup', $request->ip());

        $sql = $this->backupService->generateSqlDump();
        $filename = 'backup_' . config('database.connections.mysql.database') . '_' . now()->format('Y_m_d_His') . '.sql';

        return response($sql, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
