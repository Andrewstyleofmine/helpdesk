@extends('layout')
@section('content')

    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Регистрация</h4>
                    <form method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Введите фамилию</label>
                            <input name="surname"
                                   class="form-control @if ($errors->has('surname')) border-danger @endif"
                                   placeholder="имя"
                                   value="{{isset($_COOKIE['surname']) ? $_COOKIE['surname'] : ''}}">
                            @error('surname')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Введите имя</label>
                            <input
                                class="form-control @if ($errors->has('name')) border-danger @endif"
                                placeholder="имя"
                                name="name"
                                value="{{isset($_COOKIE['name']) ? $_COOKIE['name'] : ''}}"
                            >
                            @error('name')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Введите отчество</label>
                            <input
                                class="form-control @if ($errors->has('patronymic')) border-danger @endif"
                                placeholder="отчество"
                                name="patronymic"
                                value="{{isset($_COOKIE['patronymic']) ? $_COOKIE['patronymic'] : ''}}"

                            >
                            @error('patronymic')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>
                        <div class="form-group">
                            <label>Введите email</label>
                            <input
                                name="email"
                                class="form-control @if ($errors->has('email')) border-danger @endif"
                                placeholder="email"
                                value="{{isset($_COOKIE['email']) ? $_COOKIE['email'] : ''}}"
                            >
                            @error('email')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Введите логин</label>
                            <input
                                name="login"
                                class="form-control @if ($errors->has('login')) border-danger @endif"
                                placeholder="login"
                                value="{{isset($_COOKIE['login']) ? $_COOKIE['login'] : ''}}"
                            >
                            @error('login')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Введите пароль</label>
                            <input
                                class="form-control @if ($errors->has('password')) border-danger @endif"
                                placeholder="******"
                                type="password"
                                name="password"
                                value="{{isset($_COOKIE['password']) ? $_COOKIE['password'] : ''}}"
                            >
                            @error('password')
                            <span class="form-form__error form__error--bottom text-danger">
                                        {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="custom-file form-group">
                            <input class="form-control custom-file-input" type="file" id="lot-img"
                                   value=""
                                   name="avatar">
                            <label class="custom-file-label font-weight-bold">Выберите файл</label>
                            @error('avatar')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Зарегистрироваться">
                        </div>
                    </form>
                </article>
            </div>
        </aside>
    </div>
@endsection
