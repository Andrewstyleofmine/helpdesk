@extends('layout')
@section('content')

    <div class="error">
        <p><img src="/img/index.png" style="width: 30%"></p>
        <h1>{{$title}}</h1>
        <p>{{$message}}</p>
    </div>

@endsection
