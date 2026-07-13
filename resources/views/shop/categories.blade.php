@extends('layouts.app')

@section('title', 'Категории')

@section('content')
    <h1>Категории товаров</h1>
    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $category->name }}</h5>
                        @if($category->children->count() > 0)
                            <p class="card-text text-muted small">
                                Подкатегории:
                                @foreach($category->children as $child)
                                    <span class="badge bg-secondary">{{ $child->name }}</span>
                                @endforeach
                            </p>
                        @endif
                        <a href="{{ route('home') }}?category={{ $category->id }}" class="btn btn-primary btn-sm">Перейти</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection