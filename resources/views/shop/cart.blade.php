@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <h2 class="section-title"><i class="bi bi-cart"></i> Корзина</h2>

    @if(session('success'))
        <div class="alert alert-success alert-custom">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-custom">{{ session('error') }}</div>
    @endif

    @if(empty($products))
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3">Корзина пуста</h3>
            <a href="{{ route('home') }}" class="btn btn-primary-custom mt-3">Вернуться к покупкам</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" width="60" height="60" class="rounded me-3" style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded me-3" style="width:60px;height:60px;"></div>
                                @endif
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td>{{ number_format($product->price, 2) }} ₽</td>
                        <td>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-plus"></i></button>
                            </form>
                            <span class="mx-2 fw-bold">{{ $cart[$product->id] }}</span>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-dash"></i></button>
                            </form>
                        </td>
                        <td>{{ number_format($product->price * $cart[$product->id], 2) }} ₽</td>
                        <td>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold">Итого:</td>
                    <td class="fw-bold fs-5 text-primary">{{ number_format($totalPrice, 2) }} ₽</td>
                    <td>
                        <a href="{{ route('cart.checkout') }}" class="btn btn-success rounded-pill">Оформить заказ</a>
                        <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger rounded-pill ms-1" onclick="return confirm('Очистить корзину?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    @endif
@endsection
