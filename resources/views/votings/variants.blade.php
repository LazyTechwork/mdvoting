@extends('layouts.app')
@section('title', 'Просмотр голосования: ' . $voting->name)

@section('content')
    <variants-edit vs="{{ json_encode($voting->variants) }}"
                   sendroute="{{ route('votings.variants', ['id'=>$voting->id]) }}" csrf="{{ csrf_token() }}"></variants-edit>
@stop
