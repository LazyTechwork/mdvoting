@extends('layouts.app')
@section('title', 'Вход в систему')

@section('content')
    <h2 class="text-center mb-4">Вход в систему MDVoting</h2>
    <div class="row justify-content-center">
        <form action="{{ route('login') }}" method="post" class="col-md-6">
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
                <button type="submit" class="btn btn-outline-primary w-100">Войти</button>
            </div>
            <a href="{{ route('register') }}" class="btn btn-link d-block">Создайте аккаунт</a>
        </form>
    </div>
@stop
