<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Requests extends Model
{
    public $timestamps = false;

    public static function getStatus($status)
    {
        if ($status == 0) return 'В очереди';
        elseif ($status == 1) return 'Выполняется';
        else return 'Закрыта';
    }

    public static function getStatusId($status)
    {
        if ($status == 'В очереди') return 0;
        elseif ($status == 'Выполняется') return 1;
        elseif ($status == 'Не указано') return null;
        else return 2;
    }

    public static function getPriorityId($priority_name)
    {
        if ($priority_name == 'Низкий') return 0;
        elseif ($priority_name == 'Средний') return 1;
        elseif ($priority_name == 'Не указано') return null;
        else return 2;
    }

    public static function getPriorityName($priority_name)
    {
        if ($priority_name == 0) return 'Низкий';
        elseif ($priority_name == 1) return 'Средний';
        else return 'Высокий';
    }

    public static function getRequests()
    {
        if (Users::isClient()) {
            return Requests::whereRaw("client_id = ?", [Auth::user()->id])
                ->paginate(3);
        } elseif (Users::isStaff()) {
            return Requests::whereRaw("staff_id = ?",
                [Auth::user()->id])
                ->paginate(3);
        }
        return DB::table('requests')->orderByDesc('priority')->paginate(3);
    }

    public static function getSearchedRequests($id, $category_id, $priority, $status)
    {
        if ($id !== null and $category_id !== null and $priority !== null and $status !== null) {
            return Requests::whereRaw(
                "id = ?
                and category_id = ?
                and priority = ?
                and status = ?",
                [
                    $id,
                    $category_id,
                    $priority,
                    $status
                ]
            )->paginate(3);
        } else {
            return Requests::whereRaw(
                "id = ?
                or category_id = ?
                or priority = ?
                or status = ?",
                [
                    $id,
                    $category_id,
                    $priority,
                    $status
                ]
            )->paginate(3);
        }
    }

    public static function closeRequest($comment, $request)
    {
        $request->status = 2;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            $comment,
            Auth::user()->id,
            $request->client_id,
            $request->id,
            null
        );
        Notifications::sendEmail(
            $comment,
            Users::getEmailById($request->client_id)
        );
    }

    public static function closeRequestsByExpireDate()
    {
        $requests = Requests::all();
        foreach ($requests as $request) {
            $now = Carbon::createFromDate(date('Y-m-d H:i:s'));
            $expire_date = Carbon::createFromDate($request->date);

            if ($request->priority == 0) {
                $expire_date->addHours(48);
            } elseif ($request->priority == 1) {
                $expire_date->addHours(24);
            } else {
                $expire_date->addHours(8);
            }

            if ($now->gt($expire_date) and $request->status == 1) {
                Requests::closeRequest(
                    'Ваша заявка закрыта по истечению срока!',
                    $request
                );
            }
        }
    }

    public
    static function sendRequest($request, $category_id, $description, $file, $priority)
    {
        $request->category_id = $category_id;
        $request->description = $description;
        $request->client_id = Auth::user()->id;
        if ($file) {
            $file = new Files();
            $file->path = "/" . request()->file('file')->store('img');
            $file->save();
            $request->file_id = $file->id;

        }
        $request->date = date('Y-m-d H:i:s');
        $request->priority = $priority;

        $request->save();
        return $request;

    }

    public
    static function appoint($request, $staff_id)
    {
        $request->staff_id = $staff_id;
        $request->status = 1;
        $request->save();
        return $request;
    }

    public
    static function resendRequest($request)
    {
        $request->status = 1;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Проблема клиента не была решена!',
            Auth::user()->id,
            $request->staff_id,
            $request->id,
            null
        );
        Notifications::sendEmail(
            'Проблема клиента не была решена!',
            Users::getEmailById($request->staff_id)
        );
    }

    public static function askSupplement($request)
    {
        $request->is_edited = 1;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Необходимо дополнить содержание заяки!',
            Auth::user()->id,
            $request->client_id,
            $request->id,
            null
        );
        Notifications::sendEmail(
            'Необходимо дополнить содержание заяки!',
            Users::getEmailById($request->client_id)
        );
    }

    public
    static function deleteRequest($request)
    {
        $request->delete();
    }

    public
    static function showDate($request_date)
    {
        $now = Carbon::createFromDate(date('Y-m-d H:i:s'));
        $request_date = Carbon::createFromDate($request_date);
        $seconds = $now->diffInSeconds($request_date);
        if ($seconds < 60 and $seconds != 0) {
            $date = 'Секунд назад: ' . $seconds;
        } elseif ($seconds == 0) {
            $date = 'Только что';
        } elseif ($seconds >= 60 and $seconds < 3600) {
            $date = 'Минут назад: ' . floor($seconds / 60);
        } elseif ($seconds >= 3600 and $seconds < 86400) {
            $date = 'Часов назад: ' . floor($seconds / 3600);
        } elseif ($seconds >= 86400 and $seconds < 2592000) {
            $date = 'Дней назад: ' . floor($seconds / 86400);
        } elseif ($seconds >= 2592000 and $seconds < 31536000) {
            $date = 'Месяцев назад: ' . floor($seconds / 2592000);
        } else {
            $date = 'Лет назад: ' . floor($seconds / 31536000);
        }

        return $date;
    }

    public static function isFileShowed($request)
    {
        if ($request->file_id and $request->is_hidden) {
            if (Users::isClient()) {
                if (Auth::user()->id == $request->client_id) {
                    return true;
                }
                return false;
            } elseif (Users::isStaff()) {
                if (Auth::user()->id == $request->staff_id) {
                    return true;
                }
                return false;
            } else {
                return false;
            }

        } elseif (!$request->is_hidden and $request->file_id) {
            return true;
        } elseif (!$request->file_id) {
            return false;
        }
        return false;
    }

    public static function hideRequest($request)
    {
        $request->is_hidden = 1;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Файл заявки скрыт!',
            Auth::user()->id,
            Auth::user()->id,
            $request->id,
            null
        );
        Notifications::sendEmail(
            'Файл заявки скрыт!',
            Auth::user()->email
        );
    }

    public static function showRequest($request)
    {
        $request->is_hidden = 0;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Файл доступен для всех пользователей!',
            Auth::user()->id,
            Auth::user()->id,
            $request->id,
            null
        );
        $admin = Users::whereRaw("role = 2")->get()[0];
        Notifications::sendEmail(
            'Файл доступен для всех пользователей!',
            $admin->email,
        );
    }

    public static function refuseRequest($request)
    {
        $admin = Users::whereRaw("role = 2")->get()[0];
        $file = 'D:\OSPanel\domains\helpdesk\resources\views\mail.blade.php';
        $full_name = Users::getFullName($request->staff_id);
        $current = "{$full_name} отказался от заявки №{$request->id}";
        file_put_contents($file, $current);

        $request->staff_id = null;
        $request->status = 0;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Сортрудник отказался решать вашу проблему.
            Не беспокойтесь, заявка в ближайшее время будет назанчена на другого сотрудника!',
            Auth::user()->id,
            $request->client_id,
            $request->id,
            null
        );


        Notifications::sendEmail(
            'Один из сотурдников отказался решать проблему!',
            $admin->email,
            'HelpDesk'
        );
    }
}
