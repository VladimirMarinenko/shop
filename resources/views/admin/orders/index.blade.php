@extends('layouts.admin')

@section('title', 'Заказы')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Заказы</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="alert alert-info">
            Заказов пока нет.
        </div>
    @else
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Дата доставки</th>
                <th>Дата заказа</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->email }}</td>
                    <td>{{ $order->phone ?? '—' }}</td>
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
                    <td>
                        @if($order->delivery_date)
                            {{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y H:i') }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">👁️</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @endif
@endsection