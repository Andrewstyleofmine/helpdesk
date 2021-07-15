<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Users extends Authenticatable
{
    use Notifiable;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function isAdmin()
    {
        return Auth::user()->role == 2;
    }

    public static function isStaff()
    {
        return Auth::user()->role == 1;
    }

    public static function isClient()
    {
        return Auth::user()->role == 0;
    }

    public static function getRoleName($role_id)
    {
        if ($role_id == 0) return 'Клиент';
        elseif ($role_id == 1) return 'Сотрудник';
        else return 'Администратор';
    }

    public static function getRoleIndex($role_name)
    {
        if ($role_name == 'Клиент') return 0;
        elseif ($role_name == 'Сотрудник') return 1;
        else return 2;
    }

    public static function getFullName($id)
    {
        $user = Users::find($id);
        return "{$user->surname} {$user->name} {$user->patronymic}";
    }

    public static function getUserIdByFullName($full_name)
    {
        $full_name = explode(" ", $full_name);
        $user = Users::whereRaw("surname = ? and name = ? and patronymic = ?", [
                $full_name[0],
                $full_name[1],
                $full_name[2]
            ]
        )->get()[0];
        return $user->id;
    }

    public static function deleteUser($user)
    {
        $user->delete();
    }

    public static function signUp($surname, $name, $patronymic, $email, $login, $password)
    {
        $user = new Users();
        $user->surname = $surname;
        $user->name = $name;
        $user->patronymic = $patronymic;
        $user->email = $email;
        $user->login = $login;
        $user->password = bcrypt($password);
        $user->avatar_path = "/" . request()
                ->file('avatar')
                ->store('img');
        $user->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Вы успешно зарегистрированы!',
            $user->id,
            $user->id,
            null,
            null
        );
        Notifications::sendEmail(
            'Вы успешно зарегистрированы!',
            $user->email
        );
    }

    public static function getEmailById($id)
    {
        return Users::whereRaw("id = ?", [$id])->get()[0]->email;
    }

    public function group()
    {
        return $this->belongsTo('App\Groups');
    }

    public static function editUser($user, $role_index, $category_name, $role_name)
    {
        $user->role = $role_index;
        if ($category_name and $role_index == 1) {
            $category = Categories::whereRaw("title = ?", [$category_name])
                ->get()[0];
            $user->group_id = $category->group->id;
        } else {
            $user->group_id = null;
        }
        DB::table('notifications')
            ->whereRaw("receiver_id = ?", [$user->id])
            ->delete();
        $user->save();
        if ($user->role != $role_index) {
            Notifications::createNotification(
                date('Y-m-d H:i:s'),
                "Вам назначена роль " . $role_name,
                Auth::user()->id,
                $user->id,
                null,
                null
            );
            Notifications::sendEmail(
                "Вам назначена роль " . $role_name,
                $user->email
            );
        }
    }

    public static function removeCookies($form_values)
    {
        foreach ($form_values as $form_value) {
            setcookie($form_value, '');
        }
    }

    public static function hideModelWindow()
    {
        $client = Auth::user();
        $client->is_show_modal_window = 0;
        $client->save();
    }

    public static function getAvatar($id)
    {
        return Users::whereRaw('id = ?', [$id])->get()[0]->avatar_path;
    }

    public static function getSurname($id)
    {
        return Users::whereRaw('id = ?', [$id])->get()[0]->surname;
    }

    public static function getName($id)
    {
        return Users::whereRaw('id = ?', [$id])->get()[0]->name;
    }

    public static function getPatronymic($id)
    {
        return Users::whereRaw('id = ?', [$id])->get()[0]->patronymic;
    }

}
