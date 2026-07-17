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

            @guest
                <a href="{{ route('login') }}" class="btn btn-hero">Начать покупки</a>
            @endguest
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

            <!-- Контейнер для товаров -->
            <div id="products-container" class="row g-4">
                @include('shop.partials.products')
            </div>

            <!-- Индикатор загрузки -->
            <div id="loader" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
            </div>

            <!-- Сентринель для Infinity Scroll (невидимый элемент в конце) -->
            <div id="sentinel" style="height: 1px; width: 100%;"></div>

            <!-- Сообщение, если товары закончились -->
            <div id="end-message" class="text-center py-3 text-muted" style="display: none;">
                <i class="bi bi-check-circle"></i> Вы просмотрели все товары
            </div>
        </div>
    </div>

    <script>
            document.addEventListener('DOMContentLoaded', function() {
            let page = {{ $products->currentPage() }};
            let lastPage = {{ $products->lastPage() }};
            let isLoading = false;
            let hasMore = page < lastPage;

            const search = new URLSearchParams(window.location.search).get('search') || '';
            const category = new URLSearchParams(window.location.search).get('category') || '';

            const container = document.getElementById('products-container');
            const loader = document.getElementById('loader');
            const sentinel = document.getElementById('sentinel');
            const endMessage = document.getElementById('end-message');

            function loadMore() {
            if (isLoading || !hasMore) return;
            if (page >= lastPage) {
            hasMore = false;
            endMessage.style.display = 'block';
            return;
        }

            isLoading = true;
            page++;
            loader.style.display = 'block';

            const url = new URL('{{ route('shop.load') }}');
            url.searchParams.set('page', page);
            if (search) url.searchParams.set('search', search);
            if (category) url.searchParams.set('category', category);

            fetch(url, {
            headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
        })
            .then(response => {
            if (!response.ok) throw new Error('Ошибка загрузки');
            return response.text();
        })
            .then(html => {
            container.insertAdjacentHTML('beforeend', html);
            loader.style.display = 'none';
            isLoading = false;

            if (page >= lastPage) {
            hasMore = false;
            endMessage.style.display = 'block';
            sentinel.style.display = 'none';
        }
        })
            .catch(error => {
            console.error('Ошибка загрузки товаров:', error);
            loader.style.display = 'none';
            isLoading = false;
        });
        }

            if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && hasMore && !isLoading) {
            loadMore();
        }
        }, { rootMargin: '0px 0px 200px 0px' });
            observer.observe(sentinel);
        } else {
            window.addEventListener('scroll', function() {
            const rect = sentinel.getBoundingClientRect();
            if (rect.top <= window.innerHeight + 200 && hasMore && !isLoading) {
            loadMore();
        }
        });
        }
        });
    </script>


@endsection
