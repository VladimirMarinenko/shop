<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        $newOrders = Order::whereIn('status', ['new', 'processing'])->count();
        $productsInStock = Product::where('stock', '>', 5)->count();

        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $monthlyStats = $this->getMonthlyStats($year);
        $periodStats = $this->getPeriodStats($period, $year, $month);

        $availableYears = Order::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [now()->year];
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'newOrders',
            'productsInStock',
            'recentOrders',
            'monthlyStats',
            'periodStats',
            'availableYears',
            'period',
            'year',
            'month'
        ));
    }

    private function getMonthlyStats($year)
    {
        $monthNames = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];

        // Создаём массив для всех месяцев
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[] = [
                'month' => $monthNames[$month] . ' ' . $year,
                'year' => $year,
                'month_num' => $month,
                'orders' => 0,
                'revenue' => 0,
            ];
        }

        // Получаем статистику из БД
        $stats = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Заполняем данные по месяцам
        foreach ($stats as $stat) {
            $monthIndex = $stat->month - 1; // 0-based индекс
            if (isset($months[$monthIndex])) {
                $months[$monthIndex]['orders'] = (int) $stat->orders_count;
                $months[$monthIndex]['revenue'] = floatval($stat->total_revenue);
            }
        }

        // Отладка: проверяем результат
        // dd($months);

        return collect($months);
    }

    private function getPeriodStats($period, $year, $month)
    {
        $query = Order::where('status', 'completed');

        $monthNames = [
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь',
        ];

        switch ($period) {
            case 'month':
                $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
                $label = $monthNames[$month] . ' ' . $year;
                break;

            case 'quarter':
                $startMonth = (ceil($month / 3) - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                $query->whereYear('created_at', $year)
                    ->whereBetween(DB::raw('MONTH(created_at)'), [$startMonth, $endMonth]);
                $label = 'Q' . ceil($month / 3) . ' (' . $monthNames[$startMonth] . ' - ' . $monthNames[$endMonth] . ') ' . $year;
                break;

            case 'year':
                $query->whereYear('created_at', $year);
                $label = $year;
                break;

            case 'all':
                $label = 'Всё время';
                break;
        }

        $ordersCount = $query->count();
        $totalRevenue = $query->sum('total_price');

        return [
            'label' => $label,
            'orders' => $ordersCount,
            'revenue' => $totalRevenue,
        ];
    }
}
