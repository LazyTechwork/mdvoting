@extends('layouts.app')
@section('title', 'Статистика голосов: ' . $voting->name)

@section('content')
    <h2>Статистика голосов: {{ $voting->name }}</h2>
    <votestats vid="{{ $voting->id }}" vicode="{{ $voting->code }}"></votestats>
@stop
