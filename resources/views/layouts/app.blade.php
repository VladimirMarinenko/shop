<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Раздолье скидок')</title>
    <!-- Bootstrap + иконки -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts (лёгкий и красивый шрифт) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8f9fa; }
        .navbar {
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            background: #ffffff !important;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: #2d3436 !important;
        }
        .navbar-brand i { color: #6c5ce7; margin-right: 6px; }
        .nav-link { color: #2d3436 !important; font-weight: 500; }
        .nav-link:hover { color: #6c5ce7 !important; }
        .cart-badge {
            background: #6c5ce7;
            color: #fff;
            border-radius: 50%;
            padding: 0.15rem 0.5rem;
            font-size: 0.7rem;
            margin-left: 4px;
        }
        .hero {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: #fff;
            padding: 50px 0 40px;
            border-radius: 0 0 40px 40px;
            margin-bottom: 30px;
            text-align: center;
        }
        .hero h1 { font-size: 2.8rem; font-weight: 700; }
        .hero p { font-size: 1.2rem; opacity: 0.9; }
        .btn-hero {
            background: #fff;
            color: #6c5ce7;
            border-radius: 50px;
            padding: 0.6rem 2.2rem;
            font-weight: 600;
            border: none;
            transition: 0.2s;
        }
        .btn-hero:hover { transform: scale(1.05); background: #f0f0f0; }
        .card-product {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: #fff;
            height: 100%;
        }
        .card-product:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        .card-product img {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .card-product:hover img { transform: scale(1.03); }
        .card-product .card-body { padding: 1.2rem; }
        .card-product .card-title { font-weight: 600; font-size: 1.1rem; }
        .card-product .price { font-weight: 700; color: #2d3436; font-size: 1.3rem; }
        .btn-add {
            background: #6c5ce7;
            border: none;
            border-radius: 30px;
            padding: 0.3rem 1.2rem;
            font-weight: 600;
            color: #fff;
            transition: 0.2s;
        }
        .btn-add:hover { background: #5a4bd1; transform: scale(1.05); }
        .btn-outline-add {
            border: 1px solid #6c5ce7;
            color: #6c5ce7;
            background: transparent;
            border-radius: 30px;
            padding: 0.3rem 1.2rem;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-outline-add:hover { background: #6c5ce7; color: #fff; }
        .pagination-custom .page-link {
            border-radius: 30px;
            margin: 0 4px;
            color: #2d3436;
            border: 1px solid #e0e0e0;
        }
        .pagination-custom .page-item.active .page-link {
            background: #6c5ce7;
            border-color: #6c5ce7;
            color: #fff;
        }
        footer {
            background: #2d3436;
            color: #dfe6e9;
            padding: 30px 0;
            margin-top: 50px;
        }
        footer a { color: #dfe6e9; text-decoration: none; }
        footer a:hover { color: #fff; }
        .section-title {
            font-weight: 700;
            color: #2d3436;
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 1.5rem;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: #6c5ce7;
            border-radius: 2px;
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero { padding: 30px 0; }
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }

        <style>
         * {
             font-family: 'Inter', sans-serif;
             box-sizing: border-box;
         }
        body {
            background: #f5f7fa;
            color: #1a1a2e;
        }
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
            color: #6c5ce7;
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
        footer {
            background: #1a1a2e;
            color: #ced4da;
            padding: 40px 0;
            margin-top: 60px;
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
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.2rem; }
            .hero { padding: 40px 0; }
            .card-product img { height: 160px; }
        }

        /* Категории меню */
        .list-group-item {
            transition: background 0.15s ease;
        }
        .list-group-item a {
            transition: color 0.15s ease, transform 0.15s ease;
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

    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('img/photo_2026-02-11_18-32-54.jpg') }}" alt="Раздолье скидок" height="40">Раздолье скидок
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

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart"></i> Корзина
                        @php $cartCount = session('cart') ? array_sum(session('cart')) : 0; @endphp
                        <span class="cart-badge">{{ $cartCount }}</span>
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear"></i> Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Выйти</button>
                                </form>
                            </li>
                        </ul>
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

<p class="mb-0">&copy; {{ date('Y') }} Раздолье скидок. Все права защищены.</p>
<small class="text-muted">Сделано с ❤️</small>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
