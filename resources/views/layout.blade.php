<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>HelpDesk</title>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <style lang="scss">
        @import '../../public/css/style.css';
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="container">
            @if (Auth::check())
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">HelpDesk</a>
                </div>
                <a class="btn btn-outline-success" href="/">Главная</a>
                <a class="btn btn-outline-success" href="/requests">Заявки</a>
                @if(App\Users::isAdmin())
                    <a class="btn btn-outline-success" href="/users">Пользователи</a>
                    <a class="btn btn-outline-success" href="/categories">Категории</a>

                @else
                    <a class="btn btn-outline-success" href="/notifications">Уведомления</a>
                @endif
                <a class="btn btn-outline-primary" href="/logout">Выйти</a>
            @endif
        </div>
    </nav>
</header>
<main class="">
    <div class="container main-content badge-light">
        @yield('content')
    </div>
</main>
</body>
</html>
