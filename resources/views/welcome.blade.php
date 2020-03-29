@extends('layouts.app')

@section('content')
    @auth
        <p class="h4">Добро пожаловать в систему голосований <b>&laquo;Multi-Device Voting&raquo;</b>. Вы авторизованы
            как <b>{{ Auth::user()->username }}</b>.</p>
    @endauth
    @guest
        <p>Для создания и проведения голосований просьба авторизоваться</p>
    @endguest
@stop
