@extends('layouts.app')
@section('title', 'Просмотр голосования: ' . $voting->name)

@section('content')
    <table class="table table-responsive-md">
        <thead class="thead-dark">
        <tr>
            <th>Устройство</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>УСТРОЙСТВО</td>
            <td></td>
            <td><a href="{{ route('votings.edit', ['id' => $voting->id]) }}" class="btn btn-outline-primary w-100">Изменить</a>
            </td>
        </tr>
        </tbody>
    </table>
@stop
