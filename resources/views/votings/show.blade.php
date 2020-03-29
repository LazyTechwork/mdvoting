@extends('layouts.app')

@section('content')
    @auth
        <p class="h4 mb-5">Добро пожаловать в систему голосований <b>&laquo;Multi-Device Voting&raquo;</b>. Вы авторизованы
            как <b>{{ Auth::user()->username }}</b>.</p>

        <h3 class=font-weight-bold>Ваши голосования: <a href="{{ route('votings.new') }}" class="btn btn-outline-primary">Новое голосование</a></h3>

        @forelse($votings as $v)
            <h4><a href="#"></a>{{$v->name}}</h4>
        @empty
            <h4 class="text-secondary">Голосований не найдено</h4>
        @endforelse
    @endauth
    @guest
        <p>Для создания и проведения голосований просьба авторизоваться</p>
    @endguest
@stop
