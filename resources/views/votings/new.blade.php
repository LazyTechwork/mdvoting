@extends('layouts.app')
@section('title', 'Вход в систему')

@section('content')
    <h2 class="text-center mb-4">Новое голосование</h2>
    <div class="row justify-content-center">
        <form action="{{ route('votings.new') }}" method="post" class="col-md-6">
            @csrf
            <div class="form-group">
                <label for="name">Название голосования</label>
                <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" name="name"
                       id="name" autocomplete="name" value="{{ old('name') }}" autofocus required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary w-100">Создать</button>
            </div>
        </form>
    </div>
@stop
