@extends('layouts.admin')

@section('title', 'Товары')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Товары</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">➕ Добавить товар</a>
    </div>

    <!-- Поиск -->
    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Поиск по ID или названию..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">🔍 Найти</button>
                @if(request('search'))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Сбросить</a>
                @endif
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Цена</th>
            <th>Остаток</th>
            <th>Действия</th>
            <th>Ссылка</th> <!-- Новая колонка -->
        </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category ? $product->category->name : '—' }}</td>
                <td>{{ number_format($product->price, 2) }} ₽</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">✏️</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">🗑️</button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="btn btn-sm btn-info" title="Посмотреть на сайте">
                        👁️
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Товары не найдены</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $products->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
    </div>
@endsection
