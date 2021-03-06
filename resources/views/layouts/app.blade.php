<!doctype html>
<html lang="ru_RU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Система голосования MDVoting')</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="d-flex flex-column">
@if (!isset($navbardisabled))
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">MDVoting</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item {{ Route::is('home') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/') }}">Главная</a>
                    </li>
                    @guest
                        <li class="nav-item {{ Route::is('login') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                        <li class="nav-item {{ Route::is('register') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                        </li>
                    @endguest
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}">Выход</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
@endif
<main class="flex-grow-1">
    <div id="app" class="h-100">
        <div class="container py-5 h-100">
            @error('action_error')
            <div class="alert alert-danger" role="alert"><h5 class="alert-heading font-weight-bold">Ошибка</h5>
                <p class="mb-0">{{ $message }}</p></div>
            @enderror
            @yield('content')
        </div>
    </div>
</main>
</body>
</html>
