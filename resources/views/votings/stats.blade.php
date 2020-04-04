@extends('layouts.app')
@section('title', 'Статистика голосов: ' . $voting->name)

@section('content')
    <votestats vid="{{ $voting->id }}" vicode="{{ $voting->code }}"></votestats>
@stop
