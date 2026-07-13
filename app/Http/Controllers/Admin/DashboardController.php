<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        return view('admin.dashboard', [
            ...$this->dashboardService->getGeneralStats(),
            'recentOrders' => $this->dashboardService->getRecentOrders(),
            'monthlyStats' => $this->dashboardService->getMonthlyStats($year),
            'periodStats' => $this->dashboardService->getPeriodStats($period, $year, $month),
            'availableYears' => $this->dashboardService->getAvailableYears(),
            'period' => $period,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
