@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold">📝 Создать аккаунт</h2>
                    <p class="text-muted text-center mb-4">Зарегистрируйтесь и начните покупки</p>

                    <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Имя</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                <input id="name" type="text" class="form-control form-control-lg border-0 shadow-sm" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Владимир">
                            </div>
                            @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control form-control-lg border-0 shadow-sm" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="example@mail.ru">
                            </div>
                            @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Пароль</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control form-control-lg border-0 shadow-sm" name="password" required autocomplete="new-password" placeholder="минимум 8 символов">
                            </div>
                            @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Подтвердите пароль</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill"></i></span>
                                <input id="password_confirmation" type="password" class="form-control form-control-lg border-0 shadow-sm" name="password_confirmation" required autocomplete="new-password" placeholder="повторите пароль">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill shadow-sm">
                            <i class="bi bi-person-plus me-2"></i> Зарегистрироваться
                        </button>

                        <p class="text-center text-muted mt-3 small">
                            Уже есть аккаунт? <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Войти</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
