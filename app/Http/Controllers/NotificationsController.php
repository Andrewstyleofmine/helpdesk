<?php

namespace App\Http\Controllers;

use App\Notifications;
use App\Requests;
use App\Users;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function showNotifications()
    {
        if (Users::isClient() or Users::isStaff()) {
            $notifications = Notifications::getNotifications();
            return view('notifications', ['notifications' => $notifications]);
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы просмотреть список уведомлений вы должны быть авторизованы
                в качестве клиента или сотрудника!',
            ]);
        }
    }


}
