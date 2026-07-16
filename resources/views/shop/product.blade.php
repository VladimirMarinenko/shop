@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            @if($product->category)
                <li class="breadcrumb-item">{{ $product->category->name }}</li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded-3 shadow" alt="{{ $product->name }}" style="width: 100%; object-fit: cover;">
            @else
                <img src="https://via.placeholder.com/600x500/dfe6e9/2d3436?text=Нет+фото" class="img-fluid rounded-3 shadow" alt="Нет фото">
            @endif
        </div>
        <div class="col-md-6">
            <h1 class="display-6">{{ $product->name }}</h1>
            <p class="text-muted"><i class="bi bi-tag"></i> {{ $product->category ? $product->category->name : 'Без категории' }}</p>
            <h2 class="text-primary fw-bold mt-3">{{ number_format($product->price, 2) }} ₽</h2>

            <div class="my-3">
                @if($product->stock > 0)
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> В наличии ({{ $product->stock }} шт.)</span>
                @else
                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Нет в наличии</span>
                @endif
            </div>

            <div class="mb-4">
                <h5>Описание</h5>
                <p>{{ $product->description ?? 'Описание отсутствует.' }}</p>
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                        <i class="bi bi-cart-plus"></i> Добавить в корзину
                    </button>
                </form>
            @else
                <button class="btn btn-secondary btn-lg rounded-pill px-5" disabled>Нет в наличии</button>
            @endif

            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4 ms-2">← На главную</a>
        </div>
    </div>

    <!-- Рекомендуемые товары -->
    @if($recommended)
        <hr class="my-5">
        <h3 class="section-title">{{ $title }}</h3>
        <div class="row">
            @foreach($recommended as $item)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card card-product h-100">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->name }}" style="height: 180px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/300x180/dfe6e9/2d3436?text=Нет+фото" class="card-img-top" alt="Нет фото">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $item->name }}</h5>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price">{{ number_format($item->price, 2) }} ₽</span>
                                @if($item->stock > 0)
                                    <form action="{{ route('cart.add', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-add" title="В корзину">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Нет</span>
                                @endif
                            </div>
                            <a href="{{ route('product.show', $item->slug) }}" class="btn btn-outline-primary btn-sm mt-2">Подробнее</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
