<?php

namespace App\Services;

use App\Models\Registrasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryCards(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        return [
            'today' => Registrasi::whereDate('created_at', $today)->count(),
            'yesterday' => Registrasi::whereDate('created_at', $yesterday)->count(),
            'this_month' => Registrasi::whereBetween('created_at', [$startOfMonth, Carbon::now()])->count(),
            'last_month' => Registrasi::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count(),
        ];
    }

    public function getDailyTrend(): array
    {
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        $results = Registrasi::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            $found = $results->firstWhere('date', $date);
            $data[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    public function getMonthlyTrend(): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $results = Registrasi::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $found = $results->first(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            $data[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
