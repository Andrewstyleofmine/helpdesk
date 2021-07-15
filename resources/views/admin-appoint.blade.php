@extends('layout')
@section('content')

    <div class="row">
        <aside class="m-lg-auto">
            <div class="card">
                <article class="card-body">
                    <form method="post">
                        @csrf
                        <div class="form-group">
                            <select id="staff" name="staff" class="form-control">
                                @foreach(App\Users::all() as $user)
                                    @if($user->role == 1)
                                        <option>{{\App\Users::getFullName($user->id)}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary btn-block" value="Назначить">
                        </div>
                    </form>
                </article>
            </div>
        </aside>
    </div>

@endsection
