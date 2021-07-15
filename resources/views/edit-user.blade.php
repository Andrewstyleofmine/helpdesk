@extends('layout')
@section('content')

    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <label>Роль</label>
                            <select id="role_name" name="role_name" class="form-control">
                                @foreach($roles as $role)
                                    @if(\App\Users::getRoleName($user->role) == $role)
                                        <option selected>{{$role}}</option>
                                    @else
                                        <option>{{$role}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @if ($user->role == 1 and $user->group_id)
                            <div class="form-group">
                                <label>Решаемые проблемы</label>
                                <select id="category" name="category" class="form-control">
                                    @foreach(App\Categories::all() as $category)
                                        @if($user->group->category->title == $category->title)
                                            <option selected>{{$category->title}}</option>
                                        @else
                                            <option>{{$category->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        @elseif($user->role == 1)
                            <div class="form-group">
                                <label>Решаемые проблемы</label>
                                <select id="category" name="category" class="form-control">
                                    @foreach(App\Categories::all() as $category)

                                        <option>{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Сохранить">
                        </div>
                    </form>
                </article>
            </div>
        </aside>
    </div>

@endsection
