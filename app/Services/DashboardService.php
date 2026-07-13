<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const MONTHS = [
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

    /**
     * Получить общую статистику (карточки).
     */
    public function getGeneralStats(): array
    {
        return [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total_price'),
            'newOrders' => Order::whereIn('status', ['new', 'processing'])->count(),
            'productsInStock' => Product::where('stock', '>', 5)->count(),
        ];
    }

    /**
     * Получить последние заказы.
     */
    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Получить статистику по месяцам для указанного года.
     */
    public function getMonthlyStats(int $year): Collection
    {
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
            ->get()
            ->keyBy('month');

        return collect(range(1, 12))->map(function ($month) use ($year, $stats) {
            $stat = $stats->get($month);

            return [
                'month' => self::MONTHS[$month] . ' ' . $year,
                'year' => $year,
                'month_num' => $month,
                'orders' => $stat ? (int) $stat->orders_count : 0,
                'revenue' => $stat ? (float) $stat->total_revenue : 0,
            ];
        });
    }

    /**
     * Получить статистику за выбранный период.
     */
    public function getPeriodStats(string $period, int $year, int $month): array
    {
        $query = Order::where('status', 'completed');
        $label = $this->applyPeriodFilter($query, $period, $year, $month);

        return [
            'label' => $label,
            'orders' => $query->count(),
            'revenue' => $query->sum('total_price'),
        ];
    }

    /**
     * Получить список доступных годов.
     */
    public function getAvailableYears(): array
    {
        $years = Order::select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return empty($years) ? [now()->year] : $years;
    }

    /**
     * Применить фильтр по периоду.
     */
    private function applyPeriodFilter(&$query, string $period, int $year, int $month): string
    {
        return match ($period) {
            'month'   => $this->applyMonthFilter($query, $year, $month),
            'quarter' => $this->applyQuarterFilter($query, $year, $month),
            'year'    => $this->applyYearFilter($query, $year),
            default   => 'Всё время',
        };
    }

    private function applyMonthFilter(&$query, int $year, int $month): string
    {
        $query->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        return self::MONTHS[$month] . ' ' . $year;
    }

    private function applyQuarterFilter(&$query, int $year, int $month): string
    {
        $startMonth = (ceil($month / 3) - 1) * 3 + 1;
        $endMonth = $startMonth + 2;

        $query->whereYear('created_at', $year)
            ->whereBetween(DB::raw('MONTH(created_at)'), [$startMonth, $endMonth]);

        return 'Q' . ceil($month / 3) . ' (' . self::MONTHS[$startMonth] . ' - ' . self::MONTHS[$endMonth] . ') ' . $year;
    }

    private function applyYearFilter(&$query, int $year): string
    {
        $query->whereYear('created_at', $year);
        return (string) $year;
    }
}
