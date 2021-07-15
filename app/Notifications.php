<?php

namespace App;

use App\Mail\ContactMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Notifications extends Model
{
    public $timestamps = false;

    public static function getNotifications()
    {
        $notifications = [];
        if (Users::isStaff()) {
            $notifications = Notifications::whereRaw('group_id = ? or receiver_id = ?', [
                Auth::user()->group_id,
                Auth::user()->id
            ])->paginate(3);
        } elseif (Users::isClient()) {
            $notifications = Notifications::whereRaw('receiver_id = ?', [Auth::user()->id])->paginate(3);
        }
        return $notifications;
    }

    public static function createNotification($date, $title, $sender_id, $receiver_id, $request_id, $group_id)
    {
        $notification = new Notifications();
        $notification->date = $date;
        $notification->title = $title;
        $notification->sender_id = $sender_id;
        $notification->receiver_id = $receiver_id;
        $notification->request_id = $request_id;
        $notification->group_id = $group_id;
        $notification->save();
    }

    public static function sendEmail($theme, $user_email, $name = 'some_name')
    {
        Mail::send('mail', ['esfesf'],
            function ($msg) use ($user_email, $theme, $name) {
                $msg->to('cmdltt.2000@gmail.com')
                    ->subject($theme);
                $msg->from($user_email, $name);
            });
    }


}
