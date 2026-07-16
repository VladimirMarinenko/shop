<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка — @yield('title', 'Магазин')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Дополнительные стили -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #ced4da;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }
        .sidebar a.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .main-content {
            padding: 20px;
        }
        .navbar-brand {
            font-weight: bold;
        }

        .card-img-top {
            transition: transform 0.2s;
        }
        .card-img-top:hover {
            transform: scale(1.02);
        }

        .pagination {
            gap: 4px;
        }
        .pagination .page-link {
            border-radius: 30px !important;
            border: 1px solid #e0e0e0;
            color: #2d3436;
            font-weight: 500;
            padding: 0.4rem 0.9rem;
            transition: 0.2s;
        }
        .pagination .page-link:hover {
            background: #f5f3ff;
            border-color: #6c5ce7;
            color: #6c5ce7;
        }
        .pagination .page-item.active .page-link {
            background: #6c5ce7;
            border-color: #6c5ce7;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Боковая панель -->
        <nav class="col-md-2 d-md-block sidebar">
            <div class="position-sticky">
                <h5 class="text-white text-center">Админ-панель</h5>
                <hr class="text-white">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link" target="_blank">
                            <i class="bi bi-house-door"></i>🏠 На сайт
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Дашборд</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link">📂 Категории</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link">🛍️ Товары</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link">📦 Заказы</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link" style="color: #ced4da; border: none; text-align: left; width: 100%;">🚪 Выйти</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Основной контент -->
        <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 main-content">
            @yield('content')
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style>
    #statsBody {
        transition: all 0.3s ease;
    }

    .pagination {
        gap: 4px;
    }
    .pagination .page-link {
        border-radius: 30px !important;
        border: 1px solid #e0e0e0;
        color: #2d3436;
        font-weight: 500;
        padding: 0.4rem 0.9rem;
        transition: 0.2s;
    }
    .pagination .page-link:hover {
        background: #f5f3ff;
        border-color: #6c5ce7;
        color: #6c5ce7;
    }
    .pagination .page-item.active .page-link {
        background: #6c5ce7;
        border-color: #6c5ce7;
        color: #fff;
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
</body>
</html>
