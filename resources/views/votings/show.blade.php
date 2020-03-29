@extends('layouts.app')

@section('content')
    <h2 class="mb-2">{{ $voting->name }}</h2>
    <p>Количество участников: {{ $voting->participants->count() }} <a href="#" class="ml-2 btn btn-warning">Редактировать
            участников</a><br>
        Код для подключения к голосованию: <b>{{ $voting->code }}</b><br>
        Максимальное количество голосов: <b>{{ $voting->maxVotes }}</b><br>
        Количество вариантов для голосования: <b>{{ count($voting->variants) }}</b><br>
    </p>
@stop
