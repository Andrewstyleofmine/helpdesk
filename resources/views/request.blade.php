@extends('layout')
@section('content')
    <div class="request_info">
        <div class="col">
            <hr>
            <div class="row">
                <div class="col">
                    <H3>
                        Категория:
                    </H3>
                    <p>{{\App\Categories::getCategoryName($request->category_id)}}</p>
                </div>
                <div class="col-4">
                    <H3>
                        Описание:
                    </H3>
                    <p>{{$request->description}}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <H3>
                        Отправитель:
                    </H3>
                    <a href="/profile/{{$request->client_id}}" class="btn btn-outline-primary">
                        {{\App\Users::getFullName($request->client_id)}}
                    </a>
                </div>
                <div class="col-4">
                    <H3>
                        Исполнитель:
                    </H3>
                    @if($request->staff_id)
                        <a href="/profile/{{$request->staff_id}}" class="btn btn-outline-primary">
                            {{\App\Users::getFullName($request->staff_id)}}
                        </a>
                    @else
                        <p>Не назначен</p>
                    @endif
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <H3>
                        Отправлено:
                    </H3>
                    <p>{{\App\Requests::showDate($request->date)}}</p>
                </div>
                <div class="col-4">
                    <H3>
                        Статус:
                    </H3>
                    <p>{{\App\Requests::getStatus($request->status)}}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <H3>
                        Приоритет:
                    </H3>
                    <p>{{\App\Requests::getPriorityName($request->priority)}}</p>
                </div>
                <div class="col-4">
                    <H3>
                        Файл:
                    </H3>
                    @if(!$request->file_id)
                        <p>Отсутствует</p>
                    @elseif($is_file_showed)
                        <a class="btn btn-primary" href="/download/{{$file->id}}">Скачать</a>
                    @elseif(!$is_file_showed)
                        <p>Скрыт</p>
                    @endif

                </div>
            </div>
            <hr>
            @if(\App\Users::isStaff())

                <div class="row">

                    @if(!$request->staff_id)

                        <div class="col">
                            <a class="btn btn-primary" href="/appoint/{{$request->id}}">Назначить себя
                                исполнителем</a>
                        </div>
                    @endif
                    @if(!$request->is_edited and $request->staff_id and $request->status == 1)
                        <div class="col">
                            <a class="btn btn-primary" href="/ask-supplement/{{$request->id}}">Не хватает
                                сведений</a>
                        </div>
                    @endif
                    @if($request->status == 1)
                        <div class="col">
                            <a class="btn btn-outline-success" href="/close-request/{{$request->id}}">Закрыть
                                заявкку</a>
                        </div>
                    @endif
                    @if($request->staff_id == Auth::user()->id and $request->status == 1)
                        <div class="col">
                            <a class="btn btn-outline-success" href="/refuse/{{$request->id}}">
                                Отказаться от заявки
                            </a>
                        </div>
                    @endif

                </div>
            @endif
            @if(\App\Users::isClient())

                <div class="row">
                    @if($request->is_edited and $request->status == 1)

                        <div class="col">
                            <a class="btn btn-outline-success" href="/edit-request/{{$request->id}}">Изменить</a>
                        </div>
                    @endif
                    @if($request->status == 2)

                        <div class="col">
                            <a class="btn btn-outline-success" href="/resend-request/{{$request->id}}">
                                Отправить повторно
                            </a>
                        </div>
                        <hr>
                    @endif
                    @if(!$request->is_hidden and $request->staff_id and $request->file_id)
                        <div class="col">
                            <a class="btn btn-outline-success" href="/hide/{{$request->id}}">
                                Скрыть файл
                            </a>
                        </div>
                        <hr>
                    @endif
                    @if($request->is_hidden)
                        <div class="col">
                            <a class="btn btn-outline-success" href="/show/{{$request->id}}">
                                Открыть доступ к файлу другим пользователям
                            </a>
                        </div>
                        <hr>
                    @endif
                </div>
            @endif
            @if(\App\Users::isAdmin())
                <div class="row">
                    <div class="col">
                        <a class="btn btn-outline-success"
                           href="/admin-appoint/{{$request->id}}">
                            Назначить исполнителя
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <hr>
    @if($is_comments_showed)
        <h3 style="text-align: center">Комментарии</h3>
        @foreach($comments as $comment)
            <div class="col comment">
                <div class="row">
                    <div class="col">
                        <strong>
                            <P>
                                {{\App\Users::getFullName($comment->sender_id)}}:
                            </P>
                        </strong>
                    </div>
                    <div class="col-3">
                        <P>{{\App\Requests::showDate($comment->date)}}</P>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <p>{{$comment->message}}</p>
                    </div>
                </div>
                <hr>
            </div>
        @endforeach
        @if(count($comments) >= 3)
            <hr>
            <div class="pagination">
                {{$comments->links()}}
            </div>
            <hr>
        @endif
    @endif
    @if($is_comments_form_showed)
        <div class="row">
            <aside class="m-lg-auto">
                <div class="card">
                    <article class="card-body">
                        <form method="post">
                            @csrf
                            <div class="form-group">
                                <label>Введите комментарий</label>
                                <textarea
                                    name="message"
                                    style="width: 500px"
                                    class="form-control @if ($errors->has('message')) border-danger @endif"
                                    placeholder="Ваш комментарий">{{isset($_COOKIE['message']) ? $_COOKIE['message'] : ''}}</textarea>
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
    @endif
    <!-- HTML-код модального окна -->
    <div id="myModalBox" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Заголовок модального окна -->
                <div class="modal-header">
                    <div class="col">
                        <h4 class="modal-title">Проблема была решена качественно и быстро?</h4>
                    </div>
                    <div class="col-3">
                        <a type="button" class="close" href="/empty-answer/{{$request->id}}">×</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col">
                            <a class="btn btn-outline-danger" href="/answer-no/{{$request->id}}">Нет</a>
                        </div>
                        <div class="col">
                            <a class="btn btn-outline-primary" href="/answer-yes/{{$request->id}}">Да</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Скрипт, вызывающий модальное окно после загрузки страницы -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
            integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
            integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
            crossorigin="anonymous"></script>
    @if($is_show_modal_window)
        <script>
            $(document).ready(function () {
                $("#myModalBox").modal('show');
            });
        </script>
    @endif

@endsection
