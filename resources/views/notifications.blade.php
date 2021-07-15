@extends('layout')
@section('content')
    <div class="col notifications">
        <H2>Уведомления</H2>
        <hr>
        @foreach($notifications as $notification)
            <div class="row notification">
                <P>{{\App\Requests::showDate($notification->date)}}</P>
                <p>{{$notification->title}}</p>
                @if($notification->request_id)
                    <a class="btn btn-outline-success" href="/request/{{$notification->request_id}}">
                        Подробнее
                    </a>
                @else
                    <a class="btn btn-outline-success" href="/">
                        Подробнее
                    </a>
                @endif
            </div>
        @endforeach

        <hr>
        <div class="pagination">
            {{$notifications->links()}}
        </div>
    </div>

@endsection
