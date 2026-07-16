@extends('layouts.admin')

@section('title', 'Сборка заказа #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">📦 Сборка заказа #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
            ← Назад к заказу
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Информация о заказе</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Статус:</th>
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
                        <tr><th>Сумма:</th><td><strong>{{ number_format($order->total_price, 2) }} ₽</strong></td></tr>
                        @if($order->delivery_date)
                            <tr><th>📅 Дата доставки:</th><td>{{ \Carbon\Carbon::parse($order->delivery_date)->format('d.m.Y H:i') }}</td></tr>
                        @endif
                        <tr><th>Дата заказа:</th><td>{{ $order->created_at->format('d.m.Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Клиент</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Имя:</th><td>{{ $order->customer_name }}</td></tr>
                        <tr><th>Телефон:</th><td>{{ $order->phone ?? 'Не указан' }}</td></tr>
                        <tr><th>Адрес:</th><td>{{ $order->address ?? 'Не указан' }}</td></tr>
                        @if($order->comment)
                            <tr><th>Комментарий:</th><td>{{ $order->comment }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">🛍️ Товары для сборки</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 1.1rem;">
                    <thead class="table-light" style="font-size: 1rem;">
                    <tr>
                        <th style="width: 240px; padding: 12px 8px;">Фото</th>
                        <th style="padding: 12px 8px;">Товар</th>
                        <th style="padding: 12px 8px;">Артикул</th>
                        <th class="text-center" style="padding: 12px 8px;">Кол-во</th>
                        <th class="text-end" style="padding: 12px 8px;">Цена</th>
                        <th class="text-end" style="padding: 12px 8px;">Сумма</th>
                        <th class="text-center" style="padding: 12px 8px;">Действие</th> <!-- Новая колонка -->
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr style="padding: 12px 0;">
                            <td style="padding: 12px 8px;">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                         alt="{{ $item->product->name }}"
                                         style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; background: #f8f9fa; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                @else
                                    <div style="width: 200px; height: 200px; background: #f0f0f0; border-radius: 8px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 16px; text-align: center; padding: 10px;">
                                        нет фото
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 12px 8px; vertical-align: middle;">
                                <strong style="font-size: 1.2rem;">
                                    {{ $item->product->name ?? $item->product_name ?? 'Товар удалён' }}
                                </strong>
                                @if($item->product && $item->product->category)
                                    <br><span class="text-muted" style="font-size: 0.95rem;">{{ $item->product->category->name }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px 8px; vertical-align: middle;">
                                @if($item->product)
                                    <span style="font-size: 1.1rem; font-weight: 600; color: #2d3436;">#{{ $item->product_id }}</span>
                                @else
                                    <span class="badge bg-secondary">удалён</span>
                                @endif
                            </td>
                            <td class="text-center" style="padding: 12px 8px; vertical-align: middle; font-size: 1.2rem; font-weight: 700; color: #0d6efd;">
                                {{ $item->quantity }}
                            </td>
                            <td class="text-end" style="padding: 12px 8px; vertical-align: middle; font-size: 1.1rem;">
                                {{ number_format($item->price, 2) }} ₽
                            </td>
                            <td class="text-end" style="padding: 12px 8px; vertical-align: middle; font-size: 1.2rem; font-weight: 700;">
                                {{ number_format($item->price * $item->quantity, 2) }} ₽
                            </td>
                            <td class="text-center" style="padding: 12px 8px; vertical-align: middle;">
                                @if($item->product)
                                    <a href="{{ route('product.show', $item->product->slug) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        Подробнее о товаре
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end" style="padding: 16px 8px; font-size: 1.2rem;"><strong>Итого:</strong></td>
                        <td class="text-end" style="padding: 16px 8px; font-size: 1.5rem; font-weight: 700; color: #0d6efd;">
                            {{ number_format($order->total_price, 2) }} ₽
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
