@extends('layouts.app')
@section('title', 'Панель управления: ' . $voting->name)

@section('content')
    <h2>Панель управления голосованием: <a href="{{ route('votings.show', ['id'=>$voting->id]) }}">{{ $voting->name }}</a></h2>
    <vidash vid="{{ $voting->id }}" vicode="{{ $voting->code }}"></vidash>
@stop
