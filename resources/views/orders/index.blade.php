@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">📋 Мои заказы</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-box display-1 text-muted"></i>
            <p class="mt-3 fs-5">У вас пока нет заказов.</p>
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill px-5 mt-2">
                <i class="bi bi-arrow-left"></i> Начать покупки
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body d-flex flex-column">
                            <!-- Шапка карточки: номер и статус -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title fw-bold mb-0">
                                    <span class="badge bg-dark">#{{ $order->id }}</span>
                                </h5>
                                <span class="badge fs-6 px-3 py-2 rounded-pill
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

                            <!-- Информация о заказе -->
                            <div class="mt-2 flex-grow-1">
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> Дата</span>
                                    <span class="fw-semibold">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span class="text-muted"><i class="bi bi-tag me-1"></i> Сумма</span>
                                    <span class="fw-bold text-primary">{{ number_format($order->total_price, 2) }} ₽</span>
                                </div>
                                @if($order->delivery_date)
                                    <div class="d-flex justify-content-between py-1">
                                        <span class="text-muted"><i class="bi bi-truck me-1"></i> Доставка</span>
                                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Кнопка -->
                            <div class="mt-3">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary w-100 rounded-pill">
                                    <i class="bi bi-eye me-1"></i> Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
