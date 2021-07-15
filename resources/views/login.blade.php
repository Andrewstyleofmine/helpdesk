@extends('layout')
@section('content')

    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Войти в систему</h4>
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <label>Введите логин</label>
                            <input name="login" class="form-control @if ($errors->any()) border-danger @endif"
                                   placeholder="login">

                        </div>
                        <div class="form-group">
                            <label>Введите пароль</label>
                            <input class="form-control @if ($errors->any()) border-danger @endif"
                                   placeholder="******"
                                   type="password"
                                   name="password">
                        </div>
                        @if ($errors->any())
                            <span class="form-form__error form__error--bottom text-danger">{{ $errors->all()[0] }}</span>
                        @endif
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Войти">
                        </div>
                        <div class="form-group">
                            <a class="btn btn-primary btn-block" href="/sign-up">Регистрация</a>
                        </div>
                    </form>
                </article>
            </div>
        </aside>
    </div>
@endsection
