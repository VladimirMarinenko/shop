@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold">🔐 Вход в аккаунт</h2>
                    <p class="text-muted text-center mb-4">Войдите, чтобы продолжить покупки</p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control form-control-lg border-0 shadow-sm" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="example@mail.ru">
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
                                <input id="password" type="password" class="form-control form-control-lg border-0 shadow-sm" name="password" required autocomplete="current-password" placeholder="••••••••">
                            </div>
                            @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label for="remember_me" class="form-check-label text-muted">Запомнить меня</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary fw-semibold">
                                    Забыли пароль?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Войти
                        </button>

                        <p class="text-center text-muted mt-3 small">
                            Нет аккаунта? <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Зарегистрироваться</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection