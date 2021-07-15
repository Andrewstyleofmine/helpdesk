@extends('layout')
@section('content')

    <H2>Заявки</H2>
    <hr>
    <form method="post">
        @csrf
        <div class="row">
            <div class="col">
                <label>Номер</label>
                <input
                    type="text"
                    id="request_id"
                    name="request_id"
                    value="{{isset($_COOKIE['request_id']) ? $_COOKIE['request_id'] : ''}}"
                    class="form-control @if ($errors->has('request_id')) border-danger @endif">
                @error('request_id')
                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                </span>
                @enderror
            </div>
            <div class="col">
                <label>Категория</label>
                <select id="category" name="title"
                        class="form-control @if ($errors->has('title')) border-danger @endif">
                    @foreach($categories as $category)
                        @if(isset($_COOKIE['title']) and $_COOKIE['title'] == $category)
                            <option selected>
                                {{$_COOKIE['title']}}
                            </option>
                        @else
                            <option>{{$category}}</option>
                        @endif
                    @endforeach
                    @if(isset($_COOKIE['title']))
                        @if(!array_search($_COOKIE['title'], $categories))
                            <option selected>
                                {{$_COOKIE['title']}}
                            </option>
                        @endif
                    @endif
                </select>
                @error('title')
                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                </span>
                @enderror
            </div>
            <div class="col">
                <label>Статус</label>
                <select id="status" name="status"
                        class="form-control @if ($errors->has('status')) border-danger @endif">
                    @foreach($status_values as $status_value)
                        @if(isset($_COOKIE['status']) and $_COOKIE['status'] == $status_value)
                            <option selected>
                                {{$_COOKIE['status']}}
                            </option>
                        @else
                            <option>{{$status_value}}</option>
                        @endif

                    @endforeach
                    @if(isset($_COOKIE['status']))
                        @if(!array_search($_COOKIE['status'], $status_values))
                            <option selected>
                                {{$_COOKIE['status']}}
                            </option>
                        @endif
                    @endif
                </select>
                @error('status')
                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                </span>
                @enderror
            </div>
            <div class="col">
                <label>Приоритет</label>
                <select id="priority" name="priority"
                        class="form-control @if ($errors->has('priority')) border-danger @endif">
                    @foreach($priority_values as $priority_value)
                        @if(isset($_COOKIE['priority']) and $_COOKIE['priority'] == $priority_value)
                            <option selected>
                                {{$_COOKIE['priority']}}
                            </option>
                        @else
                            <option>{{$priority_value}}</option>
                        @endif

                    @endforeach
                    @if(isset($_COOKIE['priority']))
                        @if(!array_search($_COOKIE['priority'], $priority_values))
                            <option selected>
                                {{$_COOKIE['priority']}}
                            </option>
                        @endif
                    @endif
                </select>
                @error('priority')
                <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                </span>
                @enderror
            </div>

            <div style="padding-top: 30px" class="col">

                <input type="submit" value="Найти" class="btn btn-outline-primary" class="form-control">
            </div>
        </div>

    </form>

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
        @foreach($requests as $request)
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
        {{$requests->links()}}
    </div>



@endsection
