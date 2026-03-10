<?php

namespace App\Http\Controllers;

use App\Models\AdmissionTest;
use App\Models\Registrasi;
use App\Models\User;
use App\Services\AdminService;
use App\Services\DiscordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Jurusan;
use App\Mail\AdmissionTokenMail;
use App\Mail\AdmissionRejectedMail;
use App\Mail\TestPassedMail;
use App\Mail\TestFailedMail;
use App\Mail\TestUncertainMail;
use App\Mail\RegistrationApprovedMail;
use App\Mail\RegistrationRejectedMail;

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
        $userName = $this->resolveUserName($user);
        $adminName = $this->resolveUserName(Auth::user());
        $this->adminService->logActivity(Auth::id(), "Reset password for user: {$userName}", $request->ip());

        $this->discordService->sendNotification(
            '🔐 Password Reset',
            "Admin **{$adminName}** has reset the password for user **{$userName}**.",
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

        $userName = $this->resolveUserName($user);
        $adminName = $this->resolveUserName(Auth::user());
        $this->adminService->logActivity(Auth::id(), "Changed role for user: {$userName} from {$oldRole} to {$newRole}", $request->ip());

        $this->discordService->sendNotification(
            '🔄 Role Changed',
            "Admin **{$adminName}** changed the role of **{$userName}**.",
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

        $userName = $this->resolveUserName($user);
        $this->adminService->deleteUser($user);
        $this->adminService->logActivity(Auth::id(), "Deleted user: {$userName}", $request->ip());

        $this->discordService->sendNotification(
            '🗑️ User Deleted',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has deleted the user account for **{$userName}**.",
            15158332 // Red
        );

        return back()->with('success', 'Akun berhasil dipindahkan ke tempat sampah.');
    }

    public function restoreUser(Request $request, int $id)
    {
        $this->adminService->restoreUser($id);
        $user = \App\Models\User::findOrFail($id);
        $this->adminService->logActivity(Auth::id(), "Restored user: {$this->resolveUserName($user)}", $request->ip());

        return back()->with('success', 'Akun berhasil dipulihkan.');
    }

    public function forceDeleteUser(Request $request, int $id)
    {
        $user = \App\Models\User::withTrashed()->findOrFail($id);
        $userName = $this->resolveUserName($user);
        $this->adminService->forceDeleteUser($id);
        $this->adminService->logActivity(Auth::id(), "Permanently deleted user: {$userName}", $request->ip());

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
            ->leftJoin('jurusan as jurusan', 'jurusan.id_jurusan', '=', 'admission_tests.id_jurusan')
            ->select('admission_tests.*', 'registrasi.nisn', 'registrasi.selection_status', 'registrasi.current_stage', 'detail.nama_lengkap', 'jurusan.nama_jurusan as jurusan_nama')
            ->where('admission_tests.status', 'submitted')
            ->where('registrasi.current_stage', 'test_submitted')
            ->orderByDesc('admission_tests.submitted_at')
            ->paginate(10);

        $jurusans = Jurusan::all();
        return view('admin.tests', compact('tests', 'jurusans'));
    }

    public function viewRegistrationDocument(int $id, string $type)
    {
        $allowedDocs = ['kk', 'ijazah', 'akta_lahir', 'rapor', 'pas_foto'];
        abort_unless(in_array($type, $allowedDocs, true), 404, 'Dokumen tidak ditemukan.');

        $docPath = null;
        if (Schema::hasTable('detail_registrasi') && Schema::hasColumn('detail_registrasi', $type)) {
            $docPath = DB::table('detail_registrasi')->where('id_registrasi', $id)->value($type);
        }
        if (!$docPath && Schema::hasColumn('registrasi', $type)) {
            $docPath = Registrasi::where('id_registrasi', $id)->value($type);
        }

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
        $detail = Schema::hasTable('detail_registrasi')
            ? DB::table('detail_registrasi')->where('id_registrasi', $registrasi->id_registrasi)->first()
            : null;

        $email = ($detail->email ?? null) ?: ($registrasi->email ?? null);
        $namaLengkap = ($detail->nama_lengkap ?? null) ?: ($registrasi->nama_lengkap ?? null);
        $noHp = ($detail->no_hp ?? null) ?: ($registrasi->no_hp ?? null);
        $nik = ($detail->nik ?? null) ?: ($registrasi->nik ?? null);
        $nisn = $registrasi->nisn ?? null;
        abort_unless($email, 422, 'Email tidak ditemukan untuk pendaftaran ini.');
        abort_unless($nisn, 422, 'NISN tidak ditemukan untuk pendaftaran ini.');

        $user = User::firstOrNew(['email' => $email]);
        if (!$user->exists) {
            $user->password = Hash::make((string) $nisn, ['rounds' => 12]);
        }
        if (Schema::hasColumn('users', 'nisn')) {
            $user->nisn = $user->nisn ?: $nisn;
        }
        if (Schema::hasColumn('users', 'nik')) {
            $user->nik = $user->nik ?: $nik;
        }
        if (Schema::hasColumn('users', 'no_hp')) {
            $user->no_hp = $noHp;
        }
        if (Schema::hasColumn('users', 'nama_lengkap')) {
            $user->nama_lengkap = $namaLengkap;
        }
        if (Schema::hasColumn('users', 'role')) {
            $user->role = 1;
        }
        $user->save();

        $registrasi->update([
            'status' => 'approved',
            'current_stage' => 'test_assigned',
        ]);
        if (Schema::hasColumn('registrasi', 'id_user')) {
            $registrasi->update(['id_user' => $user->id_user]);
        }

        $this->discordService->sendNotification(
            'âœ… Registration Approved',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has **approved** the registration for **{$namaLengkap}**.",
            3066993,
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn ?? '-', 'inline' => true],
                ['name' => 'Nama', 'value' => $namaLengkap, 'inline' => true]
            ]
        );

        $adminName = $this->resolveUserName(Auth::user());
        if (!empty($email)) {
            Mail::to($email)->queue(new RegistrationApprovedMail($namaLengkap ?? 'Unknown', $registrasi->nisn ?? '-', $adminName));
        }

        return redirect()->route('admin.queue', ['status' => 'approved'])->with('success', 'Pendaftaran disetujui. Akun siswa dibuat otomatis.');
    }

    public function approveRegistrationLegacy(Request $request, int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $userColumns = Schema::getColumnListing('users');
        if (in_array('role', $userColumns, true)) {
            $user->role = 1;
        }
        if (in_array('login_token_hash', $userColumns, true)) {
            $token = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->login_token_hash = Hash::make($token);
            $user->login_token_expires_at = now()->addDays(7);
            $user->login_token_used_at = null;
        }
        if (in_array('deleted_at', $userColumns, true) && $user->trashed()) {
            $user->restore();
        }
        $user->save();

        $registrasi = Registrasi::where('id_user', $user->id_user)->orderByDesc('id_registrasi')->first();
        if ($registrasi) {
            $registrasi->update([
                'status' => 'approved',
                'current_stage' => 'test_assigned',
            ]);
            $existingTest = AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
                ->where('status', 'assigned')
                ->first();
            if (!$existingTest) {
                $testToken = Str::uuid()->toString();
                AdmissionTest::create([
                    'id_registrasi' => $registrasi->id_registrasi,
                    'token' => $testToken,
                    'test_type' => 'primary',
                    'status' => 'assigned',
                ]);
                $registrasi->update(['test_access_token' => $testToken]);
            }
        }

        $namaLengkap = $this->resolveUserName($user);
        $this->adminService->logActivity(Auth::id(), "Approved registration: {$namaLengkap}", $request->ip());
        $this->adminService->sendUserInboxMessage(
            $user,
            'approved',
            $namaLengkap,
            $this->resolveUserName(Auth::user())
        );

        if (!empty($user->email) && isset($token)) {
            Mail::to($user->email)->queue(new AdmissionTokenMail($user, $token));
        }

        $this->discordService->sendNotification(
            '✅ Registration Approved',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has **approved** the registration for **{$namaLengkap}**.",
            3066993, // Green
            [
                ['name' => 'NISN', 'value' => $user->nisn ?? '-', 'inline' => true],
                ['name' => 'Nama', 'value' => $namaLengkap, 'inline' => true]
            ]
        );

        return redirect()->route('admin.queue', ['status' => 'approved'])->with('success', 'Pendaftaran disetujui.');
    }

    public function uncertainRegistration(Request $request, int $id)
    {
        $registrasi = \App\Models\Registrasi::findOrFail($id);
        $this->adminService->uncertainRegistration($registrasi);
        $this->adminService->logActivity(Auth::id(), "Registration uncertain: {$registrasi->nama_lengkap}", $request->ip());

        $this->discordService->sendNotification(
            'Registration Uncertain',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has marked the registration for **{$registrasi->nama_lengkap}** as uncertain.",
            16776960, // Yellow
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn ?? '-', 'inline' => true],
                ['name' => 'Nama', 'value' => $registrasi->nama_lengkap, 'inline' => true]
            ]
        );

        return back()->with('success', 'Pendaftaran ditandai sebagai ragu-ragu.');
    }

    public function rejectRegistration(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $detail = Schema::hasTable('detail_registrasi')
            ? DB::table('detail_registrasi')->where('id_registrasi', $registrasi->id_registrasi)->first()
            : null;
        $email = ($detail->email ?? null) ?: ($registrasi->email ?? null);
        $namaLengkap = ($detail->nama_lengkap ?? null) ?: ($registrasi->nama_lengkap ?? 'Unknown');

        $registrasi->update([
            'status' => 'rejected',
            'current_stage' => 'closed',
        ]);

        $this->discordService->sendNotification(
            'âŒ Registration Rejected',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has **rejected** the registration for **{$namaLengkap}**.",
            15158332,
            [
                ['name' => 'NISN', 'value' => $registrasi->nisn ?? '-', 'inline' => true],
                ['name' => 'Nama', 'value' => $namaLengkap, 'inline' => true]
            ]
        );

        $adminName = $this->resolveUserName(Auth::user());
        if (!empty($email)) {
            Mail::to($email)->queue(new RegistrationRejectedMail($namaLengkap ?? 'Unknown', $registrasi->nisn ?? '-', $adminName));
        }

        return redirect()->route('admin.queue', ['status' => 'rejected'])->with('success', 'Pendaftaran ditolak.');
    }

    public function rejectRegistrationLegacy(Request $request, int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $userColumns = Schema::getColumnListing('users');
        if (in_array('role', $userColumns, true)) {
            $user->role = null;
        }
        $user->save();
        if (in_array('deleted_at', $userColumns, true)) {
            $user->delete();
        }

        $namaLengkap = $this->resolveUserName($user);
        $this->adminService->logActivity(Auth::id(), "Rejected registration: {$namaLengkap}", $request->ip());
        $this->adminService->sendUserInboxMessage(
            $user,
            'rejected',
            $namaLengkap,
            $this->resolveUserName(Auth::user())
        );

        if (!empty($user->email)) {
            Mail::to($user->email)->queue(new AdmissionRejectedMail($user));
        }

        $this->discordService->sendNotification(
            '❌ Registration Rejected',
            "Admin **" . $this->resolveUserName(Auth::user()) . "** has **rejected** the registration for **{$namaLengkap}**.",
            15158332, // Red
            [
                ['name' => 'NISN', 'value' => $user->nisn ?? '-', 'inline' => true],
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
            $this->resolveUserName(Auth::user()),
            'Lulus Seleksi - Silakan Daftar Ulang',
            "Halo {$namaLengkap}, Anda dinyatakan lulus seleksi. Silakan lanjutkan proses daftar ulang melalui tautan berikut.",
            'Daftar Ulang',
            route('user.reregister.show', $token)
        );

        $user = User::find($registrasi->id_user);
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new TestPassedMail($user, route('user.reregister.show', $token)));
        }

        return back()->with('success', 'Calon siswa dinyatakan lulus dan link daftar ulang sudah dikirim ke inbox.');
    }

    public function setTestCandidateUncertain(Request $request, int $id)
    {
        $registrasi = Registrasi::findOrFail($id);
        $namaLengkap = $this->resolveRegistrationName($registrasi);
        $validated = $request->validate([
            'recommended_jurusan_id' => ['required', 'exists:jurusan,id_jurusan'],
        ]);
        $token = Str::uuid()->toString();

        AdmissionTest::where('id_registrasi', $registrasi->id_registrasi)
            ->where('status', 'submitted')
            ->update(['status' => 'reviewed']);

        $registrasi->update([
            'selection_status' => 'uncertain',
            'current_stage' => 're_registration',
            're_registration_token' => $token,
            'recommended_jurusan_id' => $validated['recommended_jurusan_id'],
        ]);

        $this->adminService->logActivity(Auth::id(), "Set candidate uncertain after test: {$namaLengkap}", $request->ip());
        $this->adminService->sendRegistrationInboxMessage(
            $registrasi,
            'uncertain',
            $namaLengkap,
            $this->resolveUserName(Auth::user()),
            'Rekomendasi Jurusan',
            "Halo {$namaLengkap}, hasil seleksi Anda memerlukan penyesuaian jurusan. Silakan daftar ulang pada jalur yang direkomendasikan melalui tautan berikut.",
            'Daftar Ulang',
            route('user.reregister.show', $token)
        );

        $user = User::find($registrasi->id_user);
        $jurusanName = Jurusan::where('id_jurusan', $validated['recommended_jurusan_id'])->value('nama_jurusan');
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new TestUncertainMail($user, $jurusanName ?? '', route('user.reregister.show', $token)));
        }

        return back()->with('success', 'Rekomendasi jurusan dikirim dan link daftar ulang sudah tersedia.');
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
            $this->resolveUserName(Auth::user()),
            'Hasil Seleksi Akhir',
            "Halo {$namaLengkap}, setelah proses tes dan seleksi akhir, Anda belum dapat diterima pada gelombang ini. Terima kasih telah mengikuti proses pendaftaran.",
        );

        $user = User::find($registrasi->id_user);
        if ($user && $user->email) {
            Mail::to($user->email)->queue(new TestFailedMail($user));
        }

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
            ->value('nama_lengkap') ?? 'Unknown';
    }

    private function resolveUserName(User $user): string
    {
        if (!empty($user->nama_lengkap)) {
            return $user->nama_lengkap;
        }
        if (!empty($user->email)) {
            return $user->email;
        }
        return 'Unknown';
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
