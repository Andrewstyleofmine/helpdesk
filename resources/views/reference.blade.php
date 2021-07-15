@extends('layout')
@section('content')

    <div class="reference">
        <h1>Справка</h1>
        <hr/>
        <div class="row">
            <div class="col">
                <div class="card" style="width: 500px; background-color: #a2eca2">
                    <div class="card-body">
                        <h2 class="card-title">Контакты</h2>
                        <hr>
                        <p><strong>Телефон:</strong><br/> +7977733456</p>
                        <hr>
                        <p><strong>E-mail:</strong><br/> menwhohas2279@gmail.com</p>
                        <hr>
                        <p><strong>Адресс:</strong><br/> ул. Карла Маркса, д.3/48, кв 56</p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" style="width: 500px; height: 438px; background-color: #a2eca2">
                    <div class="card-body">
                        <h2 class="card-title">Мы на карте</h2>
                        <hr/>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1131.8542784037363!2d37.553234567091806!3d55.43287621025536!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sru!2sru!4v1591084820835!5m2!1sru!2sru"
                            width="455" height="325" frameborder="0" style="border:0;" allowfullscreen=""
                            aria-hidden="false"
                            tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <aside class="m-lg-auto">
                <div class="card">
                    <article class="card-body">
                        <h4 class="card-title mb-4 mt-1">Связаться с нами</h4>
                        <form method="post">
                            @csrf
                            <div class="form-group">
                                <label>Введите email</label>
                                <input
                                    value="{{isset($_COOKIE['email']) ? $_COOKIE['email'] : ''}}"
                                    type="text" class="form-control @if ($errors->has('email')) border-danger @endif"
                                    placeholder="Ваш email"
                                    name="email">
                                @error('email')
                                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Введите имя</label>
                                <input
                                    type="text"
                                    value="{{isset($_COOKIE['name']) ? $_COOKIE['name'] : ''}}"
                                    class="form-control @if ($errors->has('name')) border-danger @endif"
                                    placeholder="Ваше имя"
                                    name="name">
                                @error('name')
                                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Введите тему</label>
                                <input
                                    type="text"
                                    value="{{isset($_COOKIE['theme']) ? $_COOKIE['theme'] : ''}}"
                                    class="form-control @if ($errors->has('theme')) border-danger @endif"
                                    placeholder="Ваша тема" name="theme">
                                @error('theme')
                                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Введите сообщение</label>
                                <textarea
                                    name="message"
                                    class="form-control @if ($errors->has('message')) border-danger @endif"
                                    placeholder="Ваше сообщение">{{isset($_COOKIE['message']) ? $_COOKIE['message'] : ''}}</textarea>
                                @error('message')
                                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-block" value="Отправить">
                            </div>
                        </form>
                    </article>
                </div>
            </aside>
        </div>
    </div>

@endsection
