<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Groups;
use App\Notifications;
use App\Requests;
use App\Users;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function index()
    {
        if (Auth::check()) {
            return view('profile', [
                'avatar_path' => Auth::user()->avatar_path,
                'role_name' => Users::getRoleName(Auth::user()->role),
                'surname' => Auth::user()->surname,
                'name' => Auth::user()->name,
                'patronymic' => Auth::user()->patronymic,
                'email' => Auth::user()->email
            ]);
        }
        return redirect('login');
    }

    public function profile($id)
    {
        $user = Users::find($id);
        if (!$user) {
            return view('error', [
                'title' => '404',
                'message' => 'Пользователь не найден!',
            ]);
        } else {
            return view('profile', [
                'avatar_path' => $user->avatar_path,
                'role_name' => Users::getRoleName($user->role),
                'surname' => $user->surname,
                'name' => $user->name,
                'patronymic' => $user->patronymic,
                'email' => $user->email
            ]);
        }

    }

    public function showUsers()
    {
        if (Users::isAdmin()) {
            $users = Users::paginate(3);
            return view('users', ['users' => $users]);
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы получить доступ к списку пользователей вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
    }

    public function login()
    {

        $user = request()->validate(
            [
                'login' => 'required',
                'password' => 'required',
            ],
            [
                'required' => 'Заполните необходимые поля'
            ]
        );

        if (Auth::attempt($user)) {
            return redirect('/');
        }

        return redirect('login')->withErrors('Некорректные данные');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function showSignUp()
    {
        return view('sign-up');
    }

    public function signUp()
    {
        $form_values = ['email', 'password', 'login', 'name', 'surname', 'patronymic', 'avatar'];

        setcookie('email', request('email'));
        setcookie('password', request('password'));
        setcookie('login', request('login'));
        setcookie('name', request('name'));
        setcookie('surname', request('surname'));
        setcookie('patronymic', request('patronymic'));
        setcookie('avatar', request('avatar'));
        request()->validate(
            [
                'email' => 'required|unique:users,email|email:rfc,dns',
                'password' => 'required|regex:#^[aA-zZ0-9\-_]+$#',
                'login' => 'required',
                'name' => 'required',
                'surname' => 'required',
                'patronymic' => 'required',
                'avatar' => 'required|mimes:jpeg,png',
            ],
            [
                'required' => 'Заполните это поле',
                'unique' => 'Такой email уже существует',
                'regex' => 'Пароль должен содержать только латинские символы и цифры',
                'email' => 'Email введён некорректно',
                'mimes' => 'Изображение может быть только формата jpeg, png',
            ]
        );
        Users::signUp(
            request('surname'),
            request('name'),
            request('patronymic'),
            request('email'),
            request('login'),
            request('password')
        );
        Users::removeCookies($form_values);
        return redirect('/login');
    }

    public function showEditUser($id)
    {
        if (Users::isAdmin()) {
            $user = Users::whereRaw("id = ?", [$id])->get()[0];
            if (!$user) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Пользователь не найден!',
                ]);
            }
            $roles = ['Клиент', 'Сотрудник', 'Администратор'];
            return view('edit-user', ['user' => $user, 'roles' => $roles]);
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы назначить роль пользователю вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
    }

    public function editUser($id)
    {
        if (Users::isAdmin()) {
            $user = Users::find($id);
            if (!$user) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Пользователь не найден!',
                ]);
            }
            Users::editUser(
                Users::whereRaw("id = ?", [$id])->get()[0],
                Users::getRoleIndex(request('role_name')),
                request('category'),
                request('role_name')
            );
            return redirect('/users');
        }
        return redirect('/');
    }

    public function deleteUser($id)
    {
        if (Users::isAdmin()) {
            $user = Users::find($id);
            if (!$user) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Пользователь не найден!',
                ]);
            }
            Users::deleteUser(Users::whereRaw("id = ?", [$id])->get()[0]);
            return redirect('/users');
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы удалить пользователя вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
    }

    public function reference()
    {
        return view('reference');
    }

    public function contact()
    {
        $form_values = ['message', 'name', 'theme', 'email'];

        setcookie('message', request('message'));
        setcookie('name', request('name'));
        setcookie('theme', request('theme'));
        setcookie('email', request('email'));
        request()->validate(
            [
                'message' => 'required',
                'name' => 'required',
                'theme' => 'required',
                'email' => 'required|email:rfc,dns'
            ],
            [
                'required' => 'Заполните это поле',
                'email' => 'Email введён некорректно'
            ]
        );

        $file = 'D:\OSPanel\domains\helpdesk\resources\views\mail.blade.php';
        $current = request('message');
        file_put_contents($file, $current);

        Notifications::sendEmail(request('theme'), request('email'), request('name'));
        Users::removeCookies($form_values);
        return redirect('/reference');
    }

    public function rating()
    {
        $employees = Users::whereRaw('role = 1')->orderByDesc('positive_reviews')->get();
        $negative_reviews = [];
        $positive_reviews = [];
        foreach ($employees as $employ) {
            $negative_reviews[] = $employ->negative_reviews;
            $positive_reviews[] = $employ->positive_reviews;
        }

        $max_negative_reviews = max($negative_reviews);
        $max_positive_reviews = max($positive_reviews);

        $employees_rating = [];
        foreach ($employees as $employ) {
            $dev_positive_reviews = $employ->positive_reviews / $max_positive_reviews;
            $dev_negative_reviews = $employ->negative_reviews / $max_negative_reviews;
            $employees_rating[$employ->id] = $dev_positive_reviews - $dev_negative_reviews;
        }
        arsort($employees_rating);
        return view('rating', ['employees_rating' => $employees_rating, 'number' => 1]);
    }
}

