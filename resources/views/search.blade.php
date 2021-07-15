@extends('layout')
@section('content')
    <div style="padding-top: 60px">
        <H2 style="text-align: center">Найдены заявки</H2>
        <hr>
        <table class="table" style="margin-top: 25px">
            <thead class="badge-success">
            <tr>
                <th scope="col">№</th>
                <th scope="col">Категория</th>
                <th scope="col">Дата отправки</th>
                <th scope="col">Статус</th>
                <th scope="col">Приоритет</th>
                <th scope="col" class=""></th>
                <th scope="col" class=""></th>
                <th scope="col" class=""></th>
            </tr>
            </thead>
            <tbody>
            @foreach($searched_requests as $request)
                <tr>
                    <th scope="row">{{$request->id}}</th>
                    <td>{{\App\Categories::getCategoryName($request->category_id)}}</td>
                    <td>{{\App\Requests::showDate($request->date)}}</td>
                    <td>{{\App\Requests::getStatus($request->status)}}</td>
                    <td>{{\App\Requests::getPriorityName($request->priority)}}</td>

                    <td class=""><a class="btn btn-outline-success" href="/request/{{$request->id}}">Подробнее</a></td>
                    @if($request->is_edited and \App\Users::isClient() and $request->status == 1)
                        <td><a class="btn btn-outline-success" href="/edit-request/{{$request->id}}">Изменить</a></td>
                    @elseif(\App\Users::isAdmin())
                        <td class=""><a class="btn btn-outline-success" href="/delete-request/{{$request->id}}">Удалить</a>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(App\Users::isClient())
            <a class="btn btn-outline-primary" href="/send-request">Отправить заявку</a>
        @endif
        <hr>
        <div class="pagination">
            {{$searched_requests->links()}}
        </div>
    </div>



@endsection
