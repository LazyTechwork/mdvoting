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
<main class="flex-grow-1">
    <div class="container py-5">
        @yield('content')
    </div>
</main>
</body>
</html>
