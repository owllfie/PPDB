<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Registrasi;
use App\Models\UserInbox;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class AdminService
{
    private function registrationNameColumn(): string
    {
        if (Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
            return 'detail.nama_lengkap';
        }
        if (Schema::hasColumn('registrasi', 'nama_lengkap')) {
            return 'registrasi.nama_lengkap';
        }
        return 'users.username';
    }

    private function registrationWithDetailQuery(): Builder
    {
        $query = Registrasi::query()
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->leftJoin('users', 'users.id_user', '=', 'registrasi.id_user')
            ->select(
                'registrasi.*',
                'detail.nik',
                'detail.tempat_lahir',
                'detail.tanggal_lahir',
                'detail.jenis_kelamin',
                'detail.agama',
                DB::raw('detail.`anak_ke-` as anak_ke'),
                'detail.alamat_lengkap',
                'detail.no_hp',
                'detail.email',
                'detail.nama_ayah',
                'detail.nama_ibu',
                'detail.pekerjaan_ayah',
                'detail.pekerjaan_ibu',
                'detail.sekolah_asal',
                'detail.id_jurusan',
                'detail.kk',
                'detail.ijazah',
                'detail.akta_lahir'
            );

        if (Schema::hasColumn('detail_registrasi', 'nama_lengkap')) {
            $query->addSelect('detail.nama_lengkap');
        } elseif (Schema::hasColumn('registrasi', 'nama_lengkap')) {
            $query->addSelect('registrasi.nama_lengkap');
        } else {
            $query->addSelect(DB::raw('users.username as nama_lengkap'));
        }

        return $query;
    }

    public function logActivity(int $userId, string $action, ?string $ip = null): void
    {
        ActivityLog::create([
            'id_user' => $userId,
            'action' => $action,
            'ip_address' => $ip,
            'created_at' => Carbon::now(),
        ]);
    }

    public function getAllUsers(?string $search = null, string $sort = 'created_at', string $order = 'desc')
    {
        $query = User::whereDoesntHave('roleRelation.permissions', function ($query) {
                $query->where('slug', 'admin.access');
            })
            ->with('roleRelation');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['username', 'email', 'role', 'created_at'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)
            ->paginate(5, ['*'], 'users_page');
    }

    public function getDeletedUsers(?string $search = null, string $sort = 'deleted_at', string $order = 'desc')
    {
        $query = User::onlyTrashed()->with('roleRelation');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['username', 'email', 'deleted_at'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'deleted_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)
            ->paginate(5, ['*'], 'deleted_page');
    }

    public function getUserHistory(?string $search = null, string $sort = 'created_at', string $order = 'desc')
    {
        $query = \App\Models\UserHistory::with(['user', 'admin']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%");
                })->orWhereHas('admin', function($aq) use ($search) {
                    $aq->where('username', 'like', "%{$search}%");
                })->orWhere('field', 'like', "%{$search}%")
                  ->orWhere('old_value', 'like', "%{$search}%")
                  ->orWhere('new_value', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['created_at', 'field'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)
            ->paginate(5, ['*'], 'history_page');
    }

    public function resetPassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword, ['rounds' => 12]),
        ]);
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    public function restoreUser(int $id): void
    {
        User::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDeleteUser(int $id): void
    {
        User::withTrashed()->findOrFail($id)->forceDelete();
    }

    public function getRoles()
    {
        return \App\Models\Role::all();
    }

    public function changeRole(User $user, int $roleId, int $adminId): void
    {
        $oldRole = $user->role;
        $user->update(['role' => $roleId]);

        if ($oldRole != $roleId) {
            \App\Models\UserHistory::create([
                'id_user' => $user->id_user,
                'id_admin' => $adminId,
                'field' => 'role',
                'old_value' => $oldRole,
                'new_value' => $roleId,
            ]);
        }
    }

    public function revertHistory(int $historyId): void
    {
        $history = \App\Models\UserHistory::findOrFail($historyId);
        $user = User::withTrashed()->findOrFail($history->id_user);

        $user->update([$history->field => $history->old_value]);
        
        $history->delete();
    }

    public function getRegistrationQueue(?string $search = null, string $sort = 'created_at', string $order = 'desc', string $status = 'pending')
    {
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'pending';

        $nameColumn = $this->registrationNameColumn();
        $query = $this->registrationWithDetailQuery()
            ->where('registrasi.status', $status);

        if ($search) {
            $query->where(function($q) use ($search, $nameColumn) {
                $q->where('registrasi.nisn', 'like', "%{$search}%")
                  ->orWhere($nameColumn, 'like', "%{$search}%")
                  ->orWhere('detail.sekolah_asal', 'like', "%{$search}%")
                  ->orWhere('detail.no_hp', 'like', "%{$search}%");
            });
        }

        $sortMap = [
            'nisn' => 'registrasi.nisn',
            'nama_lengkap' => $nameColumn,
            'created_at' => 'registrasi.created_at',
            'nilai_rapor' => 'registrasi.nilai_rapor',
        ];
        $sort = $sortMap[$sort] ?? 'registrasi.created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)->paginate(5);
    }

    public function approveRegistration(Registrasi $registrasi): void
    {
        $registrasi->update(['status' => 'approved']);
    }

    public function rejectRegistration(Registrasi $registrasi): void
    {
        $registrasi->update([
            'status' => 'rejected',
            'current_stage' => 'closed',
        ]);
    }

    public function markRegistrationUncertain(Registrasi $registrasi): void
    {
        $registrasi->update(['status' => 'uncertain']);
    }

    public function sendRegistrationInboxMessage(
        Registrasi $registrasi,
        string $status,
        string $studentName,
        string $adminName,
        ?string $customSubject = null,
        ?string $customMessage = null,
        ?string $actionLabel = null,
        ?string $actionUrl = null
    ): void
    {
        if (!$registrasi->id_user) {
            return;
        }

        $subjectMap = [
            'approved' => 'Registration Approved',
            'rejected' => 'Registration Rejected',
            'uncertain' => 'Registration Needs Review',
        ];

        $messageMap = [
            'approved' => "Hello {$studentName}, your registration with NISN {$registrasi->nisn} has been approved by {$adminName}. Please check the school schedule for the next steps.",
            'rejected' => "Hello {$studentName}, your registration with NISN {$registrasi->nisn} has been rejected by {$adminName}. Please review your submitted data and contact the school if you need clarification.",
            'uncertain' => "Hello {$studentName}, your registration with NISN {$registrasi->nisn} has been marked uncertain by {$adminName}. The school needs additional review before making a final decision.",
        ];

        if (!isset($subjectMap[$status], $messageMap[$status]) && (!$customSubject || !$customMessage)) {
            return;
        }

        UserInbox::create([
            'id_user' => $registrasi->id_user,
            'subject' => $customSubject ?? $subjectMap[$status],
            'message' => $customMessage ?? $messageMap[$status],
            'status' => $status,
            'action_label' => $actionLabel,
            'action_url' => $actionUrl,
        ]);
    }

    public function uncertainRegistration(Registrasi $registrasi): void
    {
        $registrasi->update(['status' => 'uncertain']);
    }

    public function getActivityLogs(?string $search = null, string $sort = 'created_at', string $order = 'desc')
    {
        $query = ActivityLog::with('user');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%");
                })->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['created_at', 'action'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)->paginate(5);
    }

    public function getReportData(?string $startDate = null, ?string $endDate = null, ?string $search = null, string $sort = 'created_at', string $order = 'desc'): array
    {
        $nameColumn = $this->registrationNameColumn();
        $query = $this->registrationWithDetailQuery();
        $aggregateQuery = Registrasi::query()
            ->leftJoin('detail_registrasi as detail', 'detail.id_registrasi', '=', 'registrasi.id_registrasi')
            ->leftJoin('users', 'users.id_user', '=', 'registrasi.id_user');

        if ($startDate) {
            $query->whereDate('registrasi.created_at', '>=', $startDate);
            $aggregateQuery->whereDate('registrasi.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('registrasi.created_at', '<=', $endDate);
            $aggregateQuery->whereDate('registrasi.created_at', '<=', $endDate);
        }
        if ($search) {
            $query->where($nameColumn, 'like', "%{$search}%");
            $aggregateQuery->where($nameColumn, 'like', "%{$search}%");
        }

        $baseQuery = clone $query;

        $genderDistribution = (clone $aggregateQuery)->selectRaw("detail.jenis_kelamin as label, COUNT(*) as total")
            ->groupBy('detail.jenis_kelamin')
            ->pluck('total', 'label')
            ->toArray();

        $agamaDistribution = (clone $aggregateQuery)->selectRaw("detail.agama as label, COUNT(*) as total")
            ->groupBy('detail.agama')
            ->pluck('total', 'label')
            ->toArray();

        $sekolahAsalTop = (clone $aggregateQuery)->selectRaw("detail.sekolah_asal as label, COUNT(*) as total")
            ->groupBy('detail.sekolah_asal')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'label')
            ->toArray();

        $monthlyTrend = [];
        $approvedTrend = [];
        $rejectedTrend = [];

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfMonth();
            $end = Carbon::parse($endDate)->endOfMonth();
            
            while ($start->lte($end)) {
                $month = $start->month;
                $year = $start->year;

                $monthlyTrend[$start->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();
                
                $approvedTrend[$start->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'approved')->count();

                $rejectedTrend[$start->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'rejected')->count();

                $start->addMonth();
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $month = $date->month;
                $year = $date->year;

                $monthlyTrend[$date->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)->count();

                $approvedTrend[$date->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'approved')->count();

                $rejectedTrend[$date->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'rejected')->count();

                $uncertainTrend[$date->format('M Y')] = Registrasi::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'uncertain')->count();
            }
        }

        $sortMap = [
            'nama_lengkap' => $nameColumn,
            'created_at' => 'registrasi.created_at',
            'status' => 'registrasi.status',
        ];
        $sort = $sortMap[$sort] ?? 'registrasi.created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return [
            'gender' => $genderDistribution,
            'agama' => $agamaDistribution,
            'sekolah_asal' => $sekolahAsalTop,
            'monthly' => $monthlyTrend,
            'approved' => $approvedTrend,
            'rejected' => $rejectedTrend,
            'total' => $baseQuery->count('registrasi.id_registrasi'),
            'total_approved' => (clone $baseQuery)->where('registrasi.status', 'approved')->count('registrasi.id_registrasi'),
            'total_rejected' => (clone $baseQuery)->where('registrasi.status', 'rejected')->count('registrasi.id_registrasi'),
            'raw_data' => (clone $baseQuery)->orderBy($sort, $order)->paginate(5),
            'raw_data_all' => $baseQuery->orderBy($sort, $order)->get(),
        ];
    }
}
