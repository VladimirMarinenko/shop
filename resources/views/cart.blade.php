@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
    <h1>Корзина</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(empty($products))
        <p>Корзина пуста.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">На главную</a>
    @else
        <table class="table table-bordered">
            <thead class="table-dark">
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
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price, 2) }} ₽</td>
                    <td>{{ $cart[$product->id] }}</td>
                    <td>{{ number_format($product->price * $cart[$product->id], 2) }} ₽</td>
                    <td>
                        <form action="{{ route('cart.remove', $product->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-warning">−</button>
                        </form>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">+</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Итого:</strong></td>
                <td><strong>{{ number_format($totalPrice, 2) }} ₽</strong></td>
                <td>
                    <a href="{{ route('cart.checkout') }}" class="btn btn-primary">Оформить заказ</a>
                    <form action="{{ route('cart.clear') }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Очистить</button>
                    </form>
                </td>
            </tr>
            </tfoot>
        </table>
    @endif
@endsection
