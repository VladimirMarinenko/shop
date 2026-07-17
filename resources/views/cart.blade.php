@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <h2 class="section-title"><i class="bi bi-cart3 me-2"></i> Корзина</h2>

    @if(empty($products))
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h3 class="mt-3">Корзина пуста</h3>
            <p class="text-muted">Добавьте товары и возвращайтесь!</p>
            <a href="{{ route('home') }}" class="btn btn-primary btn-lg rounded-pill px-5 mt-2">
                <i class="bi bi-arrow-left me-1"></i> Вернуться к покупкам
            </a>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 40%;">Товар</th>
                            <th style="width: 15%;">Цена</th>
                            <th style="width: 20%;">Количество</th>
                            <th style="width: 15%;">Сумма</th>
                            <th class="text-center pe-3" style="width: 10%;">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" width="60" height="60" class="rounded me-3" style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-3" style="width:60px;height:60px;display:flex;align-items:center;justify-content:center;color:#999;">
                                                <i class="bi bi-image fs-4"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $product->name }}</h6>
                                            <small class="text-muted">{{ $product->category ? $product->category->name : '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold">{{ number_format($product->price, 2) }} ₽</td>
                                <td>
                                    <span class="fw-bold fs-5">{{ $cart[$product->id] }}</span>
                                </td>
                                <td class="fw-semibold text-primary">{{ number_format($product->price * $cart[$product->id], 2) }} ₽</td>
                                <td class="text-center">
                                    <form action="{{ route('cart.remove', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width:32px;height:32px;padding:0;" onclick="return confirm('Удалить товар из корзины?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light border-top">
                        <tr>
                            <td colspan="3" class="text-end fw-bold ps-3">Итого:</td>
                            <td class="fw-bold fs-5 text-primary">{{ number_format($totalPrice, 2) }} ₽</td>
                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('cart.checkout') }}" class="btn btn-success btn-sm rounded-pill px-4">
                                        Оформить заказ
                                    </a>
                                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Очистить корзину?')">
                                            Удалить все
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection
