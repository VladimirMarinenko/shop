@extends('layouts.admin')

@section('title', 'Товары')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Товары</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">➕ Добавить товар</a>
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
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
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
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $products->links('pagination::bootstrap-5') }}
@endsection
