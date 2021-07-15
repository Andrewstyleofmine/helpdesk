@extends('layout')
@section('content')
    <div class="profile">
        <H2>Профиль</H2>
        <img src="{{$avatar_path}}">
        <div class="profile-info">

            <div class="row">
                <div class="col">
                    <p><strong>Фамилия:</strong>
                        <br>
                        {{$surname}}
                    </p>
                </div>
                <div class="col">
                    <p><strong>Имя:</strong>
                        <br>
                        {{$name}}
                    </p>
                </div>
                <div class="col">
                    <p><strong>Отчество:</strong>
                        <br>
                        {{$patronymic}}
                    </p>
                </div>
                <div class="col">
                    <p><strong>Роль:</strong>
                        <br>
                        {{$role_name}}
                    </p>
                </div>
                <div class="col">
                    <p><strong>Email:</strong>
                        <br>
                        {{$email}}
                    </p>
                </div>
            </div>
        </div>
    </div>


@endsection
