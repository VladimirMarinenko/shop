@extends('layouts.admin')

@section('title', 'Создать категорию')

@section('content')
    <h1>Создать новую категорию</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Название категории</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label for="parent_id" class="form-label">Родительская категория</label>
            <select class="form-select" id="parent_id" name="parent_id">
                <option value="">— Нет (корневая категория) —</option>
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}" {{ old('parent_id') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Сохранить</button>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Назад</a>
    </form>
@endsection