@extends('layout')
@section('content')
    <H2>Рейтинг сотрудников</H2>
    <hr>
    <table class="table" style="margin-top: 25px">
        <thead class="badge-success">
        <tr>
        @foreach($employees_rating as $key => $value)
            <tr>
                <th style="font-size: 35px">{{$number++}}</th>
                <th style="font-size: 20px"><img src="{{\App\Users::getAvatar($key)}}" style="width: 90px"></th>
                <th style="font-size: 20px">{{\App\Users::getSurname($key)}}</th>
                <th style="font-size: 20px">{{\App\Users::getName($key)}}</th>
                <th style="font-size: 20px">{{\App\Users::getPatronymic($key)}}</th>
            </tr>
        @endforeach
        </thead>
    </table>
    <hr>

@endsection
