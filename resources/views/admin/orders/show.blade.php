@extends('layouts.admin')

@section('title', 'Заказ #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Заказ #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Назад к списку</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Информация о заказе</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>ID заказа:</th>
                            <td>#{{ $order->id }}</td>
                        </tr>
                        <tr>
                            <th>Статус:</th>
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
                        </tr>
                        <tr>
                            <th>Сумма:</th>
                            <td><strong>{{ number_format($order->total_price, 2) }} ₽</strong></td>
                        </tr>
                        @if($order->delivery_date)
                            <tr>
                                <th>📅 Дата доставки:</th>
                                <td><strong>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y H:i') }}</strong></td>
                            </tr>
                        @endif
                        <tr>
                            <th>Дата создания:</th>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Дата обновления:</th>
                            <td>{{ $order->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Информация о клиенте</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Имя:</th>
                            <td>{{ $order->customer_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $order->email ?? 'Не указан' }}</td>
                        </tr>
                        <tr>
                            <th>Телефон:</th>
                            <td>{{ $order->phone ?? 'Не указан' }}</td>
                        </tr>
                        @if($order->comment)
                            <tr>
                                <th>Комментарий:</th>
                                <td>{{ $order->comment }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">🛍️ Товары в заказе</h5>
                    <a href="{{ route('admin.orders.details', $order->id) }}" class="btn btn-sm btn-outline-primary">
                        📋 Подробнее (для сборки)
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Артикул</th>  <!-- Новая колонка -->
                            <th>Товар</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                            <th>Сумма</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @if($item->product)
                                        #{{ $item->product_id }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $item->product->name ?? $item->product_name ?? 'Товар удалён' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 2) }} ₽</td>
                                <td>{{ number_format($item->price * $item->quantity, 2) }} ₽</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Итого:</strong></td>
                            <td><strong>{{ number_format($order->total_price, 2) }} ₽</strong></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Изменить статус</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="new" {{ $order->status == 'new' ? 'selected' : '' }}>Новый</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>В обработке</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Выполнен</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Отменён</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Обновить статус</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
