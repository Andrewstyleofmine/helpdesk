@extends('layout')
@section('content')

    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <h4 class="card-title mb-4 mt-1">Оформление заявки</h4>
                    <form class="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Выберете категорию</label>
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
                        <div class="form-group">
                            <label>Выберете приоритет</label>
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
                        <div class="form-group">
                            <label>Опишите проблему</label>
                            <textarea
                                name="description"
                                class="form-control @if ($errors->has('description')) border-danger @endif">{{isset($_COOKIE['description']) ? $_COOKIE['description'] : ''}}</textarea>
                            @error('description')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                            </span>
                            @enderror
                        </div>
                        <div class="custom-file form-group">
                            <input
                                class="form-control custom-file-input @if ($errors->has('file')) border-danger @endif"
                                type="file" id="lot-img" value=""
                                name="file">
                            <label class="custom-file-label font-weight-bold">Выберите файл</label>
                            @error('file')
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
@endsection
