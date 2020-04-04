@extends('layouts.app')
@section('title', 'Редактирование голосования: ' . $voting->name)

@section('content')
    <h2 class="text-center mb-4">Редактирование голосования</h2>
    <div class="row justify-content-center">
        <form action="{{ route('votings.edit', ['id'=>$voting->id]) }}" method="post" class="col-md-6">
            @csrf
            <div class="form-group">
                <label for="name">Название голосования</label>
                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" name="name"
                       id="name" autocomplete="name" value="{{ old('name', $voting->name) }}" autofocus required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="maxVotes">Голосов за раз</label>
                <input type="number" min="1" class="form-control @if($errors->has('maxVotes')) is-invalid @endif"
                       name="maxVotes"
                       id="maxVotes" autocomplete="maxVotes" value="{{ old('maxVotes', $voting->maxVotes) }}" autofocus
                       required>
                @error('maxVotes')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary w-100">Отредактировать</button>
            </div>
        </form>
    </div>
@stop
