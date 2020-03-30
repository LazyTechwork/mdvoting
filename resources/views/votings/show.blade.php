@extends('layouts.app')
@section('title', 'Просмотр голосования: ' . $voting->name)

@section('content')
    <table class="table table-responsive-md">
        <thead class="thead-dark">
        <tr>
            <th>Свойство</th>
            <th>Значение</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Название голосования</td>
            <td><b>{{ $voting->name }}</b></td>
            <td><a href="{{ route('votings.edit', ['id' => $voting->id]) }}" class="btn btn-outline-primary w-100">Изменить</a>
            </td>
        </tr>
        <tr>
            <td>Количество участников</td>
            <td><b>{{ $voting->participants->count() }}</b></td>
            <td><a href="{{ route('votings.participants', ['id'=>$voting->id]) }}"
                   class="btn btn-outline-primary w-100">Редактировать участников</a></td>
        </tr>
        <tr>
            <td>Код для подключения к голосованию</td>
            <td><b>{{ $voting->code }}</b></td>
            <td><a href="{{ route('votings.resetcode', ['id'=>$voting->id]) }}" class="btn btn-outline-primary w-100">Сбросить
                    код</a></td>
        </tr>
        <tr>
            <td>Максимальное количество голосов</td>
            <td><b>{{ $voting->maxVotes }}</b></td>
            <td><a href="{{ route('votings.edit', ['id' => $voting->id]) }}" class="btn btn-outline-primary w-100">Изменить</a>
            </td>
        </tr>
        <tr>
            <td>Количество вариантов для голосования</td>
            <td><b>{{ count($voting->variants) }}</b></td>
            <td><a href="{{ route('votings.variants', ['id'=>$voting->id]) }}" class="btn btn-outline-primary w-100">Редактировать
                    варианты</a></td>
        </tr>
        <tr>
            <td>Голоса</td>
            <td><b>{{ $voting->participants->where('vote', '!=', null)->count() }}</b></td>
            <td>
                <div class="btn-group w-100">
                    <a href="#" class="btn btn-outline-primary">Статистика голосов</a>
                    <a href="#" class="btn btn-outline-primary">Сбросить голоса</a>
                </div>
            </td>
        </tr>
        <tr>
            <td>Изменения заблокированы</td>
            <td>
                <b class="{{ $voting->locked ? 'text-success' : 'text-danger' }}">{{ $voting->locked ? 'Да' : 'Нет' }}</b>
            </td>
            <td>
                <div class="btn-group w-100">
                    <a href="{{ route('votings.lock', ['id' => $voting->id]) }}"
                       class="btn btn-outline-primary">{{ $voting->locked ? 'Раз' : 'За' }}блокировать
                        изменения</a>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
@stop
