<?php

namespace App\Http\Controllers;

use App\Models\AdmissionTest;
use App\Models\Registrasi;
use App\Services\AdminService;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $currentStatus = $request->query('status', 'pending');
        $registrations = $this->adminService->getRegistrationQueue(
            $request->query('search'),
            $request->query('sort', 'created_at'),
            $request->query('order', 'desc'),
            $currentStatus
        );
        return view('admin.queue', compact('registrations', 'currentStatus'));
    }

    public function tests(Request $request)
    {
        $tests = AdmissionTest::query()
            ->join('registrasi', 'registrasi.id_registrasi', '=', 'admission_tests.id_registrasi')
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->select('admission_tests.*', 'registrasi.nisn', 'registrasi.selection_status', 'registrasi.current_stage', 'detail.nama_lengkap')
            ->where('admission_tests.status', 'submitted')
            ->where('registrasi.current_stage', 'test_submitted')
            ->orderByDesc('admission_tests.submitted_at')
            ->paginate(10);

        return view('admin.tests', compact('tests'));
    }

    public function viewRegistrationDocument(int $id, string $type)
    {
        $docPath = DB::table('detail_registrasi')
            ->where('id_registrasi', $id)
            ->value($type);

        abort_unless($docPath, 404, 'Dokumen tidak ditemukan.');
        abort_unless(Storage::disk('public')->exists($docPath), 404, 'File dokumen tidak ditemukan di storage.');

        $mime = Storage::disk('public')->mimeType($docPath) ?: 'application/octet-stream';

        return response(Storage::disk('public')->get($docPath), 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($docPath) . '"',
        ]);
    }

    public function approveRegistration(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $this->adminService->approveRegistration($registrasi);
        $namaLengkap = $this->resolveRegistrationName($registrasi);
        $this->adminService->logActivity(Auth::id(), "Approved registration: {$namaLengkap}", $request->ip());
        $test = $this->assignAdmissionTest($registrasi, 'primary');
        $this->adminService->sendRegistrationInboxMessage(
            $registrasi,
            'approved',
            $namaLengkap,
            Auth::user()->username,
            'Berkas Disetujui - Siapkan Tes Masuk',
            "Halo {$namaLengkap}, berkas pendaftaran Anda sudah disetujui. Silakan lanjutkan ke tes kemampuan dasar dan tes minat bakat melalui tombol di bawah ini.",
            'Mulai Tes',
            route('user.test.show', $test->token)
        );

        $this->discordService->sendNotification(
            '✅ Registration Approved',
            "Admin **" . Auth::user()->username . "** has **approved** the registration for **{$namaLengkap}**.",
            3066993, // Green
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn, 'inline' => true],
                ['name' => 'Nama', 'value' => $namaLengkap, 'inline' => true]
            ]
        );

        return redirect()->route('admin.queue', ['status' => 'approved'])->with('success', 'Pendaftaran disetujui.');
    }

    public function rejectRegistration(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $this->adminService->rejectRegistration($registrasi);
        $namaLengkap = $this->resolveRegistrationName($registrasi);
        $this->adminService->logActivity(Auth::id(), "Rejected registration: {$namaLengkap}", $request->ip());
        $this->adminService->sendRegistrationInboxMessage($registrasi, 'rejected', $namaLengkap, Auth::user()->username);

        $this->discordService->sendNotification(
            '❌ Registration Rejected',
            "Admin **" . Auth::user()->username . "** has **rejected** the registration for **{$namaLengkap}**.",
            15158332, // Red
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn, 'inline' => true],
                ['name' => 'Nama', 'value' => $namaLengkap, 'inline' => true]
            ]
        );

        return redirect()->route('admin.queue', ['status' => 'rejected'])->with('success', 'Pendaftaran ditolak.');
    }

    public function markRegistrationUncertain(Request $request, int $id)
    {
        abort(404);
    }

    public function passTestCandidate(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $namaLengkap = $this->resolveRegistrationName($registrasi);
        $token = Str::uuid()->toString();

        AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->where('status', 'submitted')
            ->update(['status' => 'reviewed']);

        $registrasi->update([
            'selection_status' => 'passed',
            'current_stage' => 're_registration',
            're_registration_token' => $token,
        ]);

        $this->adminService->logActivity(Auth::id(), "Passed test candidate: {$namaLengkap}", $request->ip());
        $this->adminService->sendRegistrationInboxMessage(
            $registrasi,
            'approved',
            $namaLengkap,
            Auth::user()->username,
            'Lulus Seleksi - Silakan Daftar Ulang',
            "Halo {$namaLengkap}, Anda dinyatakan lulus seleksi. Silakan lanjutkan proses daftar ulang melalui tautan berikut.",
            'Daftar Ulang',
            route('user.reregister.show', $token)
        );

        return back()->with('success', 'Calon siswa dinyatakan lulus dan link daftar ulang sudah dikirim ke inbox.');
    }

    public function setTestCandidateUncertain(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $namaLengkap = $this->resolveRegistrationName($registrasi);

        AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->where('status', 'submitted')
            ->update(['status' => 'reviewed']);

        $test = $this->assignAdmissionTest($registrasi, 'follow_up');

        $registrasi->update([
            'selection_status' => 'uncertain',
            'current_stage' => 'follow_up_test',
        ]);

        $this->adminService->logActivity(Auth::id(), "Set candidate uncertain after test: {$namaLengkap}", $request->ip());
        $this->adminService->sendRegistrationInboxMessage(
            $registrasi,
            'uncertain',
            $namaLengkap,
            Auth::user()->username,
            'Tes Lanjutan Dibutuhkan',
            "Halo {$namaLengkap}, hasil seleksi Anda masih memerlukan penilaian lanjutan. Silakan ikuti tes tambahan melalui tautan berikut.",
            'Kerjakan Tes Lanjutan',
            route('user.test.show', $test->token)
        );

        return back()->with('success', 'Calon siswa diberi status uncertain dan tes lanjutan telah dikirim.');
    }

    public function failTestCandidate(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $namaLengkap = $this->resolveRegistrationName($registrasi);

        AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->where('status', 'submitted')
            ->update(['status' => 'reviewed']);

        $registrasi->update([
            'selection_status' => 'failed',
            'current_stage' => 'closed',
            're_registration_token' => null,
        ]);

        $this->adminService->logActivity(Auth::id(), "Failed test candidate: {$namaLengkap}", $request->ip());
        $this->adminService->sendRegistrationInboxMessage(
            $registrasi,
            'rejected',
            $namaLengkap,
            Auth::user()->username,
            'Hasil Seleksi Akhir',
            "Halo {$namaLengkap}, setelah proses tes dan seleksi akhir, Anda belum dapat diterima pada gelombang ini. Terima kasih telah mengikuti proses pendaftaran.",
        );

        return back()->with('success', 'Calon siswa dinyatakan tidak lulus.');
    }

    private function resolveRegistrationName(Registrasi $registrasi): string
    {
        if (Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
            return DB::table('detail_registrasi')
                ->where('id_registrasi', $registrasi->id_registrasi)
                ->value('nama_lengkap') ?? 'Unknown';
        }

        if (Schema::hasColumn('registrasi', 'nama_lengkap')) {
            return $registrasi->nama_lengkap ?? 'Unknown';
        }

        return DB::table('users')
            ->where('id_user', $registrasi->id_user)
            ->value('username') ?? 'Unknown';
    }

    private function assignAdmissionTest(Registrasi $registrasi, string $type): AdmissionTest
    {
        $token = Str::uuid()->toString();

        $test = AdmissionTest::create([
            'id_registrasi' => $registrasi->id_registrasi,
            'token' => $token,
            'test_type' => $type,
            'status' => 'assigned',
        ]);

        $registrasi->update([
            'current_stage' => $type === 'primary' ? 'test_assigned' : 'follow_up_test',
            'test_access_token' => $token,
        ]);

        return $test;
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
