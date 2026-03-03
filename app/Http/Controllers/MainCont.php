<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class MainCont extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->roleRelation && $user->roleRelation->hasPermission('admin.dashboard')) {
            $summary = $this->dashboardService->getSummaryCards();
            $dailyTrend = $this->dashboardService->getDailyTrend();
            $monthlyTrend = $this->dashboardService->getMonthlyTrend();

            return view('dashboard.admin', compact('summary', 'dailyTrend', 'monthlyTrend'));
        }

        return view('dashboard.user');
    }
}
