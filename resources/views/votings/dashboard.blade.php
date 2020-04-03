@extends('layouts.app')
@section('title', 'Панель управления: ' . $voting->name)

@section('content')
    <vidash vid="{{ $voting->id }}" vicode="{{ $voting->code }}"></vidash>
@stop
