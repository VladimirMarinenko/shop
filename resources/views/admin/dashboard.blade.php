@extends('layouts.admin')

@section('title', 'Дашборд')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>📊 Дашборд</h1>
    </div>

    <!-- Фильтр по времени -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="period" class="form-label">Период</label>
                            <select name="period" id="period" class="form-select">
                                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Месяц</option>
                                <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Квартал</option>
                                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Год</option>
                                <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Всё время</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="year" class="form-label">Год</label>
                            <select name="year" id="year" class="form-select">
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2" id="month-container" style="{{ $period == 'month' || $period == 'quarter' ? '' : 'display: none;' }}">
                            <label for="month" class="form-label">Месяц</label>
                            <select name="month" id="month" class="form-select">
                                @php
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
                                @endphp
                                @foreach($monthNames as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Показать</button>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info mb-0">
                                <strong>{{ $periodStats['orders'] }}</strong> заказов на сумму
                                <strong>{{ number_format($periodStats['revenue'], 0) }} ₽</strong>
                                <br>
                                <small>за период: <strong>{{ $periodStats['label'] }}</strong></small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистические карточки -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Товары</h5>
                    <p class="card-text display-6">{{ $totalProducts ?? 0 }}</p>
                    <small>Всего товаров в магазине</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Новые заказы</h5>
                    <p class="card-text display-6">{{ $newOrders ?? 0 }}</p>
                    <small>Требуют внимания</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Всего заказов</h5>
                    <p class="card-text display-6">{{ $totalOrders ?? 0 }}</p>
                    <small>Всего заказов</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Доход</h5>
                    <p class="card-text display-6">{{ number_format($totalRevenue ?? 0, 0) }} ₽</p>
                    <small>Общая выручка</small>
                </div>
            </div>
        </div>
    </div>

    <!-- График статистики по месяцам -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">📈 Статистика продаж по месяцам ({{ $year }})</h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="toggleStatsBtn">
                        <span id="toggleIcon">▼</span> <span id="toggleText">Развернуть</span>
                    </button>
                </div>
                <div class="card-body" id="statsBody" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-dark">
                            <tr>
                                <th>Месяц</th>
                                <th class="text-center">Заказов</th>
                                <th class="text-end">Выручка</th>
                                <th>Прогресс</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($monthlyStats as $stat)
                                <tr>
                                    <td>{{ $stat['month'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $stat['orders'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($stat['revenue'], 0) }} ₽</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success"
                                                 role="progressbar"
                                                 style="width: {{ $stat['orders'] > 0 ? '100%' : '0%' }}"
                                                 aria-valuenow="{{ $stat['orders'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="{{ $stat['orders'] > 0 ? $stat['orders'] : 1 }}">
                                                {{ $stat['orders'] > 0 ? $stat['orders'] . ' заказов' : '' }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-secondary">
                            <tr>
                                <th><strong>Итого за {{ $year }}</strong></th>
                                <th class="text-center">{{ $monthlyStats->sum('orders') }}</th>
                                <th class="text-end">{{ number_format($monthlyStats->sum('revenue'), 0) }} ₽</th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Последние заказы и наличие товаров -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">🕐 Последние заказы</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ number_format($order->total_price, 2) }} ₽</td>
                                    <td>
                                        @switch($order->status)
                                            @case('new')
                                            <span class="badge bg-primary">Новый</span>
                                            @break
                                            @case('processing')
                                            <span class="badge bg-warning text-dark">В обработке</span>
                                            @break
                                            @case('completed')
                                            <span class="badge bg-success">Выполнен</span>
                                            @break
                                            @case('cancelled')
                                            <span class="badge bg-danger">Отменён</span>
                                            @break
                                            @default
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                           class="btn btn-sm btn-info">👁️</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">Заказов пока нет.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Наличие товаров -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📦 Наличие товаров</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Всего товаров:</span>
                            <span class="fw-bold">{{ $totalProducts }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary"
                                 role="progressbar"
                                 style="width: 100%"
                                 aria-valuenow="100"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>✅ В наличии (&gt; 5 шт.):</span>
                            <span class="fw-bold text-success">{{ $productsInStock }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                 role="progressbar"
                                 style="width: {{ $totalProducts > 0 ? ($productsInStock / $totalProducts) * 100 : 0 }}%"
                                 aria-valuenow="{{ $productsInStock }}"
                                 aria-valuemin="0"
                                 aria-valuemax="{{ $totalProducts }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary w-100">
                            📋 Управлять товарами
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.getElementById('toggleStatsBtn');
            var statsBody = document.getElementById('statsBody');
            var toggleIcon = document.getElementById('toggleIcon');
            var toggleText = document.getElementById('toggleText');

            if (toggleBtn && statsBody) {
                toggleBtn.addEventListener('click', function() {
                    if (statsBody.style.display === 'none') {
                        statsBody.style.display = '';
                        toggleIcon.textContent = '▲';
                        toggleText.textContent = 'Свернуть';
                    } else {
                        statsBody.style.display = 'none';
                        toggleIcon.textContent = '▼';
                        toggleText.textContent = 'Развернуть';
                    }
                });
            }
        });
    </script>
@endsection
