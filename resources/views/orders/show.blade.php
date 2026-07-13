@extends('layouts.app')

@section('title', 'Заказ #' . $order->id)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Мои заказы</a></li>
            <li class="breadcrumb-item active" aria-current="page">Заказ #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="section-title mb-0">
            📦 Заказ #{{ $order->id }}
        </h2>
        <span class="badge fs-5 px-4 py-2 rounded-pill
            @switch($order->status)
        @case('new') bg-primary @break
        @case('processing') bg-warning text-dark @break
        @case('completed') bg-success @break
        @case('cancelled') bg-danger @break
        @default bg-secondary
            @endswitch
            ">
            @switch($order->status)
                @case('new') <i class="bi bi-clock-history me-1"></i> Новый @break
                @case('processing') <i class="bi bi-arrow-repeat me-1"></i> В обработке @break
                @case('completed') <i class="bi bi-check-circle-fill me-1"></i> Выполнен @break
                @case('cancelled') <i class="bi bi-x-circle-fill me-1"></i> Отменён @break
                @default {{ $order->status }}
            @endswitch
        </span>
    </div>

    <div class="row g-4">
        <!-- Левая колонка -->
        <div class="col-md-6">
            <!-- Информация о заказе -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i> Информация о заказе</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Статус:</th>
                            <td>
                                @switch($order->status)
                                    @case('new') <span class="badge bg-primary">Новый</span> @break
                                    @case('processing') <span class="badge bg-warning text-dark">В обработке</span> @break
                                    @case('completed') <span class="badge bg-success">Выполнен</span> @break
                                    @case('cancelled') <span class="badge bg-danger">Отменён</span> @break
                                    @default <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Сумма:</th>
                            <td><strong class="text-primary fs-5">{{ number_format($order->total_price, 2) }} ₽</strong></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Дата заказа:</th>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        @if($order->delivery_date)
                            <tr>
                                <th class="text-muted">Дата доставки:</th>
                                <td><i class="bi bi-truck me-1"></i> {{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Данные получателя -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="card-title mb-0"><i class="bi bi-person me-2"></i> Данные получателя</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Имя:</th>
                            <td>{{ $order->customer_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email:</th>
                            <td>{{ $order->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Телефон:</th>
                            <td>{{ $order->phone ?? 'Не указан' }}</td>
                        </tr>
                        @if($order->comment)
                            <tr>
                                <th class="text-muted">Комментарий:</th>
                                <td>{{ $order->comment }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Правая колонка -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-3">
                    <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i> Состав заказа</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Товар</th>
                                <th class="text-center">Кол-во</th>
                                <th class="text-end">Сумма</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}" width="40" height="40" class="rounded me-2" style="object-fit: cover;">
                                            @else
                                                <div class="bg-secondary rounded me-2" style="width:40px;height:40px;"></div>
                                            @endif
                                            <span>{{ $item->product->name ?? $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price * $item->quantity, 2) }} ₽</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end"><strong>Итого:</strong></td>
                                <td class="text-end"><strong class="text-primary fs-5">{{ number_format($order->total_price, 2) }} ₽</strong></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Назад к заказам
        </a>
    </div>
@endsection
