@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <h2 class="section-title"><i class="bi bi-clipboard-check"></i> Оформление заказа</h2>

    @if(session('error'))
        <div class="alert alert-danger alert-custom">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-custom">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="mb-3">Ваши данные</h4>
                <form action="{{ route('cart.placeOrder') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-bold">Имя *</label>
                        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">Телефон *</label>
                        <input type="tel" class="form-control form-control-lg" id="phone" name="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-bold">Комментарий</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3">{{ old('comment') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label fw-bold">Дата и время доставки</label>
                        <input type="datetime-local" class="form-control form-control-lg" id="delivery_date" name="delivery_date" value="{{ old('delivery_date') }}">
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill">
                        <i class="bi bi-check-circle"></i> Подтвердить заказ
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2 rounded-pill">Назад в корзину</a>
                </form>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="mb-3">Состав заказа</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Кол-во</th>
                            <th>Сумма</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $cart[$product->id] }}</td>
                                <td>{{ number_format($product->price * $cart[$product->id], 2) }} ₽</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Итого:</td>
                            <td class="fw-bold fs-5 text-primary">{{ number_format($totalPrice, 2) }} ₽</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
