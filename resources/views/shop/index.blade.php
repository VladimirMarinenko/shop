@extends('layouts.app')

@section('title', 'Главная')

@section('content')

    @if(isset($pageTitle) && $products->isEmpty())
        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i> По вашему запросу ничего не найдено. Попробуйте изменить ключевые слова.
        </div>
    @endif

    <!-- Hero -->
    <div class="hero">
        <div class="container">
            <h1>🔥 Откройте мир лучших товаров</h1>
            <p>Качественные товары по выгодным ценам — только у нас</p>
            <a href="#products" class="btn btn-hero">Начать покупки</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Боковое меню -->
        <div class="col-lg-3">
            @include('partials.categories-menu')
        </div>

        <!-- Товары -->
        <div class="col-lg-9">
            @if(isset($category))
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            @endif

                @if(isset($pageTitle))
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                            <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                        </ol>
                    </nav>
                @endif

            <h2 class="section-title">
                @if(isset($category))
                    {{ $category->name }}
                @else
                    Все товары
                @endif
            </h2>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-xl-4">
                        <div class="card card-product h-100">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <img src="https://placehold.co/300x220/e0e0e0/6c5ce7?text=Нет+фото" class="card-img-top" alt="Нет фото">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="text-muted small">{{ $product->category ? $product->category->name : 'Без категории' }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="price">{{ number_format($product->price, 2) }} ₽</span>
                                    @if($product->stock > 0)
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-add"><i class="bi bi-cart-plus"></i></button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary">Нет</span>
                                    @endif
                                </div>
                                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-add w-100 mt-2">Подробнее</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="mt-3">Товары не найдены.</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
