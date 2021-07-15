@extends('layout')
@section('content')
    <div class="col categories">
        <H2>Категории</H2>
        <div class="row">
            <aside class="m-lg-auto">
                <div class="card">
                    <article class="card-body">
                        <form method="post">
                            @csrf
                            <div class="form-group">
                                <label>Введите название категории</label>
                                <input name="title" class="form-control" placeholder="title">
                            </div>
                            @error('title')
                            <span class="form-form__error form__error--bottom text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-block" value="Добавить категорию">
                            </div>
                        </form>
                    </article>
                </div>
            </aside>
        </div>
        <hr>
        @foreach($categories as $category)
            <div class="row category">
                <P>{{$category->title}}</P>
                <a class="btn btn-outline-success" href="/delete-category/{{$category->id}}">
                    Удалить
                </a>
            </div>
        @endforeach
        <hr>
        <div class="pagination">
            {{$categories->links()}}
        </div>
    </div>
@endsection
