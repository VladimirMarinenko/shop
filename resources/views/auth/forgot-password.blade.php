@extends('layouts.app')

@section('title', 'Восстановление пароля')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-center mb-4 fw-bold">🔑 Восстановление пароля</h2>
                    <p class="text-muted text-center mb-4">Введите email, и мы отправим ссылку для сброса</p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-3" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control form-control-lg border-0 shadow-sm" name="email" value="{{ old('email') }}" required autofocus placeholder="example@mail.ru">
                            </div>
                            @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                            <i class="bi bi-send me-2"></i> Отправить ссылку
                        </button>

                        <p class="text-center text-muted mt-3 small">
                            <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">← Вернуться ко входу</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
