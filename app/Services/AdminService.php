<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Registrasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminService
{
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

    public function getRegistrationQueue(?string $search = null, string $sort = 'created_at', string $order = 'desc')
    {
        $query = Registrasi::where('status', 'pending');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('sekolah_asal', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['nisn', 'nama_lengkap', 'created_at', 'nilai_rapor'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $order)->paginate(5);
    }

    public function approveRegistration(Registrasi $registrasi): void
    {
        $registrasi->update(['status' => 'approved']);
    }

    public function rejectRegistration(Registrasi $registrasi): void
    {
        $registrasi->update(['status' => 'rejected']);
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
        $query = Registrasi::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($search) {
            $query->where('nama_lengkap', 'like', "%{$search}%");
        }

        $baseQuery = clone $query;

        $genderDistribution = (clone $query)->selectRaw("jenis_kelamin, COUNT(*) as total")
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin')
            ->toArray();

        $agamaDistribution = (clone $query)->selectRaw("agama, COUNT(*) as total")
            ->groupBy('agama')
            ->pluck('total', 'agama')
            ->toArray();

        $sekolahAsalTop = (clone $query)->selectRaw("sekolah_asal, COUNT(*) as total")
            ->groupBy('sekolah_asal')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'sekolah_asal')
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
            }
        }

        $allowedSorts = ['nama_lengkap', 'created_at', 'status'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        return [
            'gender' => $genderDistribution,
            'agama' => $agamaDistribution,
            'sekolah_asal' => $sekolahAsalTop,
            'monthly' => $monthlyTrend,
            'approved' => $approvedTrend,
            'rejected' => $rejectedTrend,
            'total' => $baseQuery->count(),
            'total_approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'total_rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'raw_data' => (clone $baseQuery)->orderBy($sort, $order)->paginate(5),
            'raw_data_all' => $baseQuery->orderBy($sort, $order)->get(),
        ];
    }
}
