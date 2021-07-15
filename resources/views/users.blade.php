@extends('layout')
@section('content')
    <H2>Пользователи</H2>
    <hr>
    <table class="table" style="margin-top: 25px">
        <thead class="badge-success">
        <tr>
            <th scope="col">Фамилия</th>
            <th scope="col">Имя</th>
            <th scope="col">Отчество</th>
            <th scope="col">email</th>
            <th scope="col">Роль</th>
            <th scope="col">Решаемые проблемы</th>
            <th scope="col"></th>
            <th scope="col"></th>

        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->surname}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->patronymic}}</td>
                <td>{{$user->email}}</td>
                <td>{{\App\Users::getRoleName($user->role)}}</td>
                @if($user->group_id)
                    <td>{{$user->group->category->title}}</td>
                @else
                    <td>---</td>
                @endif
                <td class=""><a class="btn btn-outline-success" href="/edit-user/{{$user->id}}">Изменть</a></td>
                <td class=""><a class="btn btn-outline-success" href="/delete-user/{{$user->id}}">Удалить</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    <div class="pagination">
        {{$users->links()}}
    </div>


@endsection
