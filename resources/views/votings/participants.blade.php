@extends('layouts.app')
@section('title', 'Редактирование голосования: ' . $voting->name)

@section('content')
    <h2 class="text-center mb-4">Редактирование голосования</h2>
    <div class="row justify-content-center">
        <form action="{{ route('votings.participants', ['id'=>$voting->id]) }}" method="post" class="col-md-6"
              enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="list">Excel файл с участниками голосования</label>
                <input type="file" class="form-control-file @if($errors->has('list')) is-invalid @endif" name="list"
                       id="list"
                       accept=".csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                       required>
                @error('list')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rewrite" id="mergeLists" value="0" checked>
                <label class="form-check-label" for="mergeLists">
                    Объединить списки
                </label>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="radio" name="rewrite" id="rewriteLists" value="1">
                <label class="form-check-label" for="rewriteLists">
                    Перезаписать полностью
                </label>
                @error('rewrite')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary w-100">Обновить список участников</button>
            </div>
        </form>
        <table class="table table-responsive-md table-striped">
            <thead class="thead-dark">
            <tr>
                <th>Группа</th>
                <th>Участник</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($voting->participants as $p)
                <tr>
                    <td>{{ $p->group }}</td>
                    <td>{{ $p->name }}</td>
                    <td class="{{ $p->vote ? 'text-success' : 'text-danger' }}">{{ $p->vote ? 'Голоса засчитаны' : 'Не голосовал' }}</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop
