@extends('layouts.app')
@section('title', 'Вход в систему')

@section('content')
    <h2 class="text-center mb-4">Регистрация в системе MDVoting</h2>
    <div class="row justify-content-center">
        <form action="{{ route('register') }}" method="post" class="col-md-6">
            @csrf
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" class="form-control @if($errors->has('username')) is-invalid @endif" name="username" id="username" autocomplete="username" value="{{ old('username') }}" autofocus required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" class="form-control @if($errors->has('password')) is-invalid @endif" name="password" id="password" autocomplete="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Повторите пароль</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" autocomplete="password_confirmation" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary w-100">Зарегистрироваться</button>
            </div>
            <a href="{{ route('register') }}" class="btn btn-link d-block">Уже зарегистрированы?</a>
        </form>
    </div>
@stop
