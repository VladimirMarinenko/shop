<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Раздолье скидок')</title>
    <!-- Bootstrap + иконки -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* ── Глобальные стили ── */
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #f5f7fa;
            color: #1a1a2e;
        }
        main {
            flex: 1 0 auto;  /* занимает всё свободное пространство */
        }
        footer {
            flex-shrink: 0;
            background: #1a1a2e;
            color: #ced4da;
            padding: 30px 0;
            margin-top: 40px;
            border-radius: 40px 40px 0 0;
        }
        footer a {
            color: #ced4da;
            transition: 0.2s;
        }
        footer a:hover {
            color: #fff;
            text-decoration: none;
        }

        /* ── Навигация ── */
        .navbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: #1a1a2e !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
        .navbar-brand i {
            color: #6c5ce7;
            margin-right: 8px;
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-link {
            color: #1a1a2e !important;
            font-weight: 500;
            transition: 0.2s;
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #6c5ce7;
            transition: 0.3s;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .cart-badge {
            background: #6c5ce7;
            color: #fff;
            border-radius: 50%;
            padding: 0.15rem 0.55rem;
            font-size: 0.7rem;
            margin-left: 4px;
            font-weight: 600;
        }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            border-radius: 0 0 40px 40px;
            padding: 60px 0 50px;
            margin-bottom: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: -1px;
            position: relative;
            z-index: 1;
        }
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .btn-hero {
            background: #fff;
            color: #6c5ce7;
            border-radius: 50px;
            padding: 0.7rem 2.5rem;
            font-weight: 600;
            border: none;
            transition: 0.3s;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }
        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
            background: #f8f9fa;
        }

        /* ── Карточки товаров ── */
        .card-product {
            border: none;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            height: 100%;
            overflow: hidden;
        }
        .card-product:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 50px rgba(108, 92, 231, 0.12);
        }
        .card-product img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .card-product:hover img {
            transform: scale(1.05);
        }
        .card-product .card-body {
            padding: 1.5rem;
        }
        .card-product .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
            color: #1a1a2e;
        }
        .card-product .price {
            font-weight: 700;
            font-size: 1.4rem;
            color: #2d3436;
            letter-spacing: -0.5px;
        }
        .btn-add {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            border: none;
            border-radius: 30px;
            padding: 0.4rem 1.2rem;
            color: #fff;
            font-weight: 600;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.25);
        }
        .btn-add:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.35);
        }
        .btn-outline-add {
            border: 1px solid #e0e0e0;
            background: transparent;
            border-radius: 30px;
            padding: 0.3rem 1.2rem;
            font-weight: 500;
            transition: 0.3s;
            color: #1a1a2e;
        }
        .btn-outline-add:hover {
            border-color: #6c5ce7;
            background: #f5f3ff;
            color: #6c5ce7;
        }

        /* ── Разное ── */
        .section-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: #1a1a2e;
            margin-bottom: 1.8rem;
            position: relative;
            padding-bottom: 12px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #6c5ce7, #a29bfe);
            border-radius: 2px;
        }
        .list-group-item {
            border: none;
            padding: 0.6rem 1.2rem;
            background: transparent;
            transition: 0.2s;
        }
        .list-group-item a {
            color: #2d3436;
            font-weight: 500;
        }
        .list-group-item a:hover {
            color: #6c5ce7 !important;
            transform: translateX(4px);
        }
        .list-group-item a i {
            transition: transform 0.15s ease;
        }
        .list-group-item a:hover i {
            transform: scale(1.1);
        }
        .badge-soft {
            background: rgba(108, 92, 231, 0.1);
            color: #6c5ce7;
            font-weight: 600;
            padding: 0.3rem 0.7rem;
            border-radius: 30px;
        }
        .card-shadow {
            border: none;
            border-radius: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .card-shadow:hover {
            box-shadow: 0 12px 45px rgba(0,0,0,0.1);
        }
        .breadcrumb {
            background: transparent;
            padding: 0.5rem 0;
        }
        .breadcrumb-item a {
            color: #6c5ce7;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #1a1a2e;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero { padding: 40px 0; }
            .card-product img { height: 160px; }
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
        .badge {
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/photo_2026-02-11_18-32-54.jpg') }}" alt="Раздолье скидок">
            Раздолье скидок
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item me-2">
                    <form action="{{ route('shop.search') }}" method="GET" class="d-flex">
                        <input class="form-control form-control-sm me-1" type="search" name="q" placeholder="Поиск..." aria-label="Search" value="{{ request('q') }}">
                        <button class="btn btn-outline-light btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </li>

                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Админка
                            </a>
                        </li>
                    @endif
                @endauth

                <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-box"></i> Мои заказы</a></li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart"></i> Корзина
                        @php $cartCount = session('cart') ? array_sum(session('cart')) : 0; @endphp
                        <span class="cart-badge">{{ $cartCount }}</span>
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <span class="nav-link" style="color: rgba(255,255,255,0.9); cursor: default;">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link" style="color: rgba(255,255,255,0.55); border: none; background: transparent; padding: 0.5rem 0;">
                                <i class="bi bi-box-arrow-right"></i> Выйти
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Войти</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Регистрация</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="py-3">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </div>
</main>

<footer>
    <div class="container text-center">
        <p class="mb-0">&copy; {{ date('Y') }} Раздолье скидок. Все права защищены.</p>
        <small class="text-muted">Сделано с ❤️</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
