<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Role;
use App\Models\Permission;

class AdminController extends Controller
{
    protected AdminService $adminService;
    protected DiscordService $discordService;

    public function __construct(AdminService $adminService, DiscordService $discordService)
    {
        $this->adminService = $adminService;
        $this->discordService = $discordService;
    }

    public function users(Request $request)
    {
        $users = $this->adminService->getAllUsers(
            $request->query('search_now'),
            $request->query('sort_now', 'created_at'),
            $request->query('order_now', 'desc')
        );
        $deletedUsers = $this->adminService->getDeletedUsers(
            $request->query('search_deleted'),
            $request->query('sort_deleted', 'deleted_at'),
            $request->query('order_deleted', 'desc')
        );
        $history = $this->adminService->getUserHistory(
            $request->query('search_history'),
            $request->query('sort_history', 'created_at'),
            $request->query('order_history', 'desc')
        );
        $roles = $this->adminService->getRoles();
        return view('admin.users', compact('users', 'deletedUsers', 'history', 'roles'));
    }

    public function resetPassword(Request $request, int $id)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        if ($user->roleRelation && $user->roleRelation->hasPermission('admin.access')) {
            abort(403, 'Anda tidak dapat meriset password administrator dengan hak akses tinggi.');
        }

        $this->adminService->resetPassword($user, $request->password);
        $this->adminService->logActivity(Auth::id(), "Reset password for user: {$user->username}", $request->ip());

        $this->discordService->sendNotification(
            '🔐 Password Reset',
            "Admin **" . Auth::user()->username . "** has reset the password for user **{$user->username}**.",
            15105570 // Orange
        );

        return back()->with('success', 'Password berhasil direset.');
    }

    public function changeRole(Request $request, int $id)
    {
        $request->validate([
            'role' => ['required', 'exists:roles,id_role'],
        ]);

        $user = \App\Models\User::findOrFail($id);

        if ($user->roleRelation && $user->roleRelation->hasPermission('admin.access')) {
            abort(403, 'Anda tidak dapat mengubah role administrator dengan hak akses tinggi.');
        }

        $oldRole = $user->roleRelation->role_name ?? "Role ID: {$user->role}";
        $this->adminService->changeRole($user, $request->role, Auth::id());
        $user->load('roleRelation');
        $newRole = $user->roleRelation->role_name ?? "Role ID: {$request->role}";

        $this->adminService->logActivity(Auth::id(), "Changed role for user: {$user->username} from {$oldRole} to {$newRole}", $request->ip());

        $this->discordService->sendNotification(
            '🔄 Role Changed',
            "Admin **" . Auth::user()->username . "** changed the role of **{$user->username}**.",
            15844367, // Gold
            [
                ['name' => 'Old Role', 'value' => $oldRole, 'inline' => true],
                ['name' => 'New Role', 'value' => $newRole, 'inline' => true]
            ]
        );

        return back()->with('success', 'Role berhasil diubah.');
    }

    public function deleteUser(Request $request, int $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if ($user->roleRelation && $user->roleRelation->hasPermission('admin.access')) {
            abort(403, 'Anda tidak dapat menghapus akun administrator dengan hak akses tinggi.');
        }

        $username = $user->username;
        $this->adminService->deleteUser($user);
        $this->adminService->logActivity(Auth::id(), "Deleted user: {$username}", $request->ip());

        $this->discordService->sendNotification(
            '🗑️ User Deleted',
            "Admin **" . Auth::user()->username . "** has deleted the user account for **{$username}**.",
            15158332 // Red
        );

        return back()->with('success', 'Akun berhasil dipindahkan ke tempat sampah.');
    }

    public function restoreUser(Request $request, int $id)
    {
        $this->adminService->restoreUser($id);
        $user = \App\Models\User::findOrFail($id);
        $this->adminService->logActivity(Auth::id(), "Restored user: {$user->username}", $request->ip());

        return back()->with('success', 'Akun berhasil dipulihkan.');
    }

    public function forceDeleteUser(Request $request, int $id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $username = $user->username;
        $this->adminService->forceDeleteUser($id);
        $this->adminService->logActivity(Auth::id(), "Permanently deleted user: {$username}", $request->ip());

        return back()->with('success', 'Akun berhasil dihapus secara permanen.');
    }

    public function revertHistory(Request $request, int $id)
    {
        $this->adminService->revertHistory($id);
        $this->adminService->logActivity(Auth::id(), "Reverted user modification (History ID: {$id})", $request->ip());

        return back()->with('success', 'Perubahan berhasil dibatalkan.');
    }

    public function queue(Request $request)
    {
        $registrations = $this->adminService->getRegistrationQueue(
            $request->query('search'),
            $request->query('sort', 'created_at'),
            $request->query('order', 'desc')
        );
        return view('admin.queue', compact('registrations'));
    }

    public function approveRegistration(Request $request, int $id)
    {
        $registrasi = \App\Models\Registrasi::findOrFail($id);
        $this->adminService->approveRegistration($registrasi);
        $this->adminService->logActivity(Auth::id(), "Approved registration: {$registrasi->nama_lengkap}", $request->ip());

        $this->discordService->sendNotification(
            'Registration Approved',
            "Admin **" . Auth::user()->username . "** has **approved** the registration for **{$registrasi->nama_lengkap}**.",
            3066993, // Green
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn, 'inline' => true],
                ['name' => 'Nama', 'value' => $registrasi->nama_lengkap, 'inline' => true]
            ]
        );

        return back()->with('success', 'Pendaftaran disetujui.');
    }

    public function uncertainRegistration(Request $request, int $id)
    {
        $registrasi = \App\Models\Registrasi::findOrFail($id);
        $this->adminService->uncertainRegistration($registrasi);
        $this->adminService->logActivity(Auth::id(), "Registration uncertain: {$registrasi->nama_lengkap}", $request->ip());

        $this->discordService->sendNotification(
            'Registration Uncertain',
            "Admin **" . Auth::user()->username . "** has marked the registration for **{$registrasi->nama_lengkap}** as uncertain.",
            16776960, // Yellow
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn, 'inline' => true],
                ['name' => 'Nama', 'value' => $registrasi->nama_lengkap, 'inline' => true]
            ]
        );

        return back()->with('success', 'Pendaftaran ditandai sebagai ragu-ragu.');
    }

    public function rejectRegistration(Request $request, int $id)
    {
        $registrasi = \App\Models\Registrasi::findOrFail($id);
        $this->adminService->rejectRegistration($registrasi);
        $this->adminService->logActivity(Auth::id(), "Rejected registration: {$registrasi->nama_lengkap}", $request->ip());

        $this->discordService->sendNotification(
            'Registration Rejected',
            "Admin **" . Auth::user()->username . "** has **rejected** the registration for **{$registrasi->nama_lengkap}**.",
            15158332, // Red
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn, 'inline' => true],
                ['name' => 'Nama', 'value' => $registrasi->nama_lengkap, 'inline' => true]
            ]
        );

        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function reports(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');

        $reportData = $this->adminService->getReportData($startDate, $endDate, $search, $sort, $order);
        return view('admin.reports', compact('reportData', 'startDate', 'endDate', 'search', 'sort', 'order'));
    }

    public function exportReports(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');

        $data = $this->adminService->getReportData($startDate, $endDate, $search, $sort, $order);
        $registrations = $data['raw_data_all'];

        $filename = "Laporan_Pendaftaran_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Nama Lengkap', 'Tanggal Daftar', 'Status'];

        $callback = function() use($registrations, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($registrations as $index => $reg) {
                fputcsv($file, [
                    $index + 1,
                    $reg->nama_lengkap,
                    $reg->created_at->format('Y-m-d H:i:s'),
                    $reg->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');

        $reportData = $this->adminService->getReportData($startDate, $endDate, $search, $sort, $order);
        
        $pdf = Pdf::loadView('admin.reports_pdf', compact('reportData', 'startDate', 'endDate'));
        return $pdf->download("Laporan_Pendaftaran_" . date('Ymd_His') . ".pdf");
    }

    public function logs(Request $request)
    {
        $logs = $this->adminService->getActivityLogs(
            $request->query('search'),
            $request->query('sort', 'created_at'),
            $request->query('order', 'desc')
        );
        return view('admin.logs', compact('logs'));
    }

    public function access(Request $request)
    {
        $search = $request->query('search');
        $roles = Role::where('id_role', '!=', 4)->get();
        $permissions = Permission::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
        })->get();
        return view('admin.access', compact('roles', 'permissions', 'search'));
    }

    public function updateAccess(Request $request)
    {
        $permissions = $request->input('permissions', []);
        
        DB::transaction(function () use ($permissions) {
            $roles = Role::where('id_role', '!=', 4)->get();
            /** @var \App\Models\Role $role */
            foreach ($roles as $role) {
                $rolePermissions = $permissions[$role->id_role] ?? [];
                $role->permissions()->sync($rolePermissions);
            }
        });

        $this->adminService->logActivity(Auth::id(), "Updated role access permissions", $request->ip());

        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }
}
