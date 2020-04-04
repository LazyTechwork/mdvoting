@extends('layouts.app')
@section('title', 'Статистика голосов: ' . $voting->name)

@section('content')
    <h2 class="mb-5">Статистика голосов: <a href="{{ route('votings.show', ['id'=>$voting->id]) }}">{{ $voting->name }}</a></h2>
    <votestats vid="{{ $voting->id }}" vicode="{{ $voting->code }}"></votestats>
@stop
