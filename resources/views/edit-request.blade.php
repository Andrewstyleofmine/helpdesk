@extends('layout')
@section('content')
    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <h4 class="card-title mb-4 mt-1 ">Оформление заявки</h4>
                    <form class="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Выберете категорию</label>
                            <select id="category" name="category" class="form-control">
                                @foreach(App\Categories::all() as $category)
                                    @if($category->title == App\Categories::getCategoryName($category->id))
                                        <option selected>{{$category->title}}</option>
                                    @else
                                        <option>{{$category->title}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Опишите проблему</label>
                            <textarea name="description" class="form-control">{{$request->description}}</textarea>
                        </div>
                        <div class="custom-file form-group">
                            <input class="form-control custom-file-input" type="file" id="lot-img" value=""
                                   name="file">
                            <label class="custom-file-label font-weight-bold">Выберите файл</label>
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
