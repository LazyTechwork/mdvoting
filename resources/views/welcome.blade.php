@extends('layouts.app')

@section('content')
    @auth
        <p>{{ Auth::user()->username }}</p>
    @endauth
    @guest
        <p>Не авторизован</p>
    @endguest
@stop
