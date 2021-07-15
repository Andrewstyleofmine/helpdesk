<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Comments;
use App\Files;
use App\Groups;
use App\Notifications;
use App\Requests;
use App\Users;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\CodeCleaner\UseStatementPass;

class RequestsController extends Controller
{
    public function showRequests()
    {
        if (Auth::check()) {
            $requests = Requests::getRequests();
            $priority_values = ['Низкий', 'Средний', 'Высокий', 'Не указано'];
            $status_values = ['В очереди', 'Выполняется', 'Закрыта', 'Не указано'];
            $categories = [];
            $categories_object = Categories::all();
            foreach ($categories_object as $categories_object_element) {
                $categories[] = $categories_object_element->title;
            }
            Requests::closeRequestsByExpireDate();
            return view('requests', [
                'requests' => $requests,
                'priority_values' => $priority_values,
                'status_values' => $status_values,
                'categories' => $categories
            ]);
        }
        return redirect('/');
    }

    public function search($id, $category_id, $priority, $status)
    {

        $category_id = Categories::getCategoryId($category_id);
        $priority = Requests::getPriorityId($priority);
        $status = Requests::getStatusId($status);
        $searched_requests = Requests::getSearchedRequests($id, $category_id, $priority, $status);
        return view('search', ['searched_requests' => $searched_requests]);

    }

    public function showSendRequest()
    {
        if (Users::isClient()) {
            $priority_values = ['Низкий', 'Средний', 'Высокий'];
            $categories = [];
            $categories_object = Categories::all();
            foreach ($categories_object as $categories_object_element) {
                $categories[] = $categories_object_element->title;
            }
            return view('send-request', ['priority_values' => $priority_values, 'categories' => $categories]);
        }
        return redirect('/');
    }

    public function sendRequest()
    {

        $form_values = ['description', 'title', 'priority'];

        setcookie('description', request('description'));
        setcookie('title', request('title'));
        setcookie('priority', request('priority'));

        $priority_values = ['Низкий', 'Средний', 'Высокий'];
        $search_index = array_search(request('priority'), $priority_values);
        if (!$search_index and request('priority') != 'Низкий') {
            return redirect('/send-request')->withErrors(['priority' => 'Некоректно указан приоритет']);
        };

        request()->validate(
            [
                'description' => 'required',
                'file' => 'mimes:jpeg,png',
                'title' => 'exists:categories',
            ],
            [
                'required' => 'Заполните необходимые поля',
                'mimes' => 'Файл может быть только формата jpeg, png',
                'exists' => 'Данной категории не существует'
            ]
        );


        $request = Requests::sendRequest(
            new Requests(),
            Categories::getCategoryId(request('title')),
            request('description'),
            request('file'),
            Requests::getPriorityId(request('priority'))
        );
        if ($request->priority == 0) {
            Notifications::createNotification(
                date('Y-m-d H:i:s'),
                'Новая заявка!',
                Auth::user()->id,
                null,
                $request->id,
                Groups::getGroupId($request->category_id)
            );
        }
        Users::removeCookies($form_values);
        return redirect('/requests');
    }

    public function showEditRequest($id)
    {
        if (Users::isClient()) {
            $request = Requests::find($id);

            if ($request) {
                if ($request->is_edited) {
                    return view('edit-request', ['request' => $request]);
                } else {
                    return view('error', [
                        'title' => '404',
                        'message' => 'Внесение дополнительных сведений в содержание заявки не требуется!',
                    ]);
                }
            } else {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы редактировать заявку вы должны быть авторизованы в качестве клиента!',
            ]);
        }

    }

    public function editRequest($id)
    {
        $request = Requests::sendRequest(
            Requests::find($id),
            Categories::getCategoryId(request('category')),
            request('description'),
            request('file'),
            Requests::getPriorityId(request('priority'))
        );

        $request->is_edited = 0;
        $request->save();
        Notifications::createNotification(
            date('Y-m-d H:i:s'),
            'Содержание заявки изменено!',
            Auth::user()->id,
            $request->staff_id,
            $request->id,
            null
        );

        Notifications::sendEmail(
            'Содержание заявки изменено!',
            Users::getEmailById($request->staff_id)
        );
        return redirect('requests');
    }

    public function showRequest($id)
    {
        $is_comments_showed = false;
        $is_comments_form_showed = false;
        $is_show_modal_window = false;
        $request = Requests::find($id);
        $comments = Comments::whereRaw('request_id = ?', [$id])->paginate(3);
        if (count($comments) > 0 and $request->status == 1) {
            if (Auth::user()->id == $request->staff_id or Auth::user()->id == $request->client_id) {
                $is_comments_showed = true;
            }
        }
        if ($request->status == 1) {
            if (Auth::user()->id == $request->staff_id or Auth::user()->id == $request->client_id) {
                $is_comments_form_showed = true;
            }
        }
        if (Auth::user()->is_show_modal_window and Auth::user()->id == $request->client_id) {
            if ($request->status == 2) {
                $is_show_modal_window = true;
            }
        }

        if (!$request) {
            return view('error', [
                'title' => '404',
                'message' => 'Заявка не найдена!',
            ]);
        }
        $file = null;
        $is_file_showed = Requests::isFileShowed($request);
        if ($request->file_id) $file = Files::find($request->file_id);
        return view('request', [
            'request' => $request,
            'file' => $file,
            'is_file_showed' => $is_file_showed,
            'comments' => $comments,
            'is_comments_showed' => $is_comments_showed,
            'is_comments_form_showed' => $is_comments_form_showed,
            'is_show_modal_window' => $is_show_modal_window
        ]);
    }

    public function appoint($id)
    {
        if (Users::isStaff()) {
            $request = Requests::appoint(
                Requests::find($id),
                Auth::user()->id
            );
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Notifications::createNotification(
                date('Y-m-d H:i:s'),
                'Ваша заявка в статусе выполнения!',
                Auth::user()->id,
                $request->client_id,
                $request->id,
                null
            );
            Notifications::sendEmail(
                'Ваша заявка в статусе выполнения!',
                Users::getEmailById($request->client_id)
            );
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы назначить заявку вы должны быть авторизованы
                в качестве администратора или сотрудника!',
            ]);
        }
        return redirect("/request/{$id}");
    }

    public function showAdminAppoint($id)
    {
        if (Users::isAdmin()) {
            return view('admin-appoint');
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы назначить заявку вы должны быть авторизованы
                в качестве администратора или сотрудника!',
            ]);
        }
    }

    public function adminAppoint($id)
    {
        if (Users::isAdmin()) {

            $request = Requests::appoint(
                Requests::find($id),
                Users::getUserIdByFullName(request('staff'))
            );
            Notifications::createNotification(
                date('Y-m-d H:i:s'),
                'Вам назначена заявка!',
                Auth::user()->id,
                $request->staff_id,
                $request->id,
                null
            );
            Notifications::sendEmail(
                'Вам назначена заявка!',
                Users::getEmailById($request->staff_id)
            );
        }
        return redirect('/');
    }

    public function askSupplement($id)
    {
        if (Users::isStaff()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::askSupplement(Requests::find($id));
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы отправить запрос на доработку заявки вы должны быть авторизованы
                в качестве сотрудника!',
            ]);
        }
        return redirect('/');
    }

    public function closeRequest($id)
    {
        if (Users::isStaff()) {
            $request = Requests::find($id);

            $client = Users::whereRaw('id = ?', [$request->client_id])->get()[0];
            $client->is_show_modal_window = 1;
            $client->save();

            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::closeRequest('Ваша заявка закрыта!', $request);
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы закрыть заявку вы должны быть авторизованы
                в качестве сотрудника!',
            ]);
        }
        return redirect("/request/{$id}");

    }

    public function resendRequest($id)
    {
        if (Users::isClient()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::resendRequest(Requests::find($id));
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы отправить заявку повторно вы должны быть авторизованы
                в качестве клиента!',
            ]);
        }
        return redirect('/');
    }

    public function deleteRequest($id)
    {
        if (Users::isAdmin()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::deleteRequest(Requests::find($id));
            return redirect("/requests");
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы удалить заявку вы должны быть авторизованы в качестве администратора!',
            ]);
        }
    }

    public function searchRequests()
    {
        $form_values = ['request_id', 'title', 'status', 'priority'];

        setcookie('request_id', request('request_id'));
        setcookie('title', request('title'));
        setcookie('status', request('status'));
        setcookie('priority', request('priority'));

        $priority_values = ['Низкий', 'Средний', 'Высокий', 'Не указано'];
        $status_values = ['В очереди', 'Выполняется', 'Закрыта', 'Не указано'];
        $search_index = array_search(request('priority'), $priority_values);
        $status_index = array_search(request('status'), $status_values);
        $errors = [];

        if (!$search_index and request('priority') != 'Низкий') {
            $errors['priority'] = 'Некоректно указан приоритет';
        }
        if (!$status_index and request('status') != 'В очереди') {
            $errors['status'] = 'Некоректно указан статус';
        }

        if ($errors) {
            return redirect('/requests')->withErrors($errors);
        }

        request()->validate(
            [
                'request_id' => 'required|integer',
                'title' => 'exists:categories',
                'status' => 'required',
                'priority' => 'required',
            ],
            [
                'required' => 'Заполните это поле',
                'integer' => 'Значение поля должно быть числовым',
                'exists' => 'Данной категории не существует'

            ]
        );

        $id = request('request_id');
        $category_id = request('title');
        $priority = request('priority');
        $status = request('status');

        Users::removeCookies($form_values);
        return redirect("/search/{$id}/{$category_id}/{$priority}/{$status}");

    }

    public function hide($id)
    {
        if (Users::isClient()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::hideRequest($request);
            return redirect("/request/{$id}");
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы скрыть файл заявки вы должны быть авторизованы
                в качестве клиента!',
            ]);
        }
    }

    public function show($id)
    {
        if (Users::isClient()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::showRequest($request);
            return redirect("/request/{$id}");

        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы открыть доступ к файлу заявки вы должны быть авторизованы
                в качестве клиента!',
            ]);
        }
    }

    public function refuse($id)
    {
        if (Users::isStaff()) {
            $request = Requests::find($id);
            if (!$request) {
                return view('error', [
                    'title' => '404',
                    'message' => 'Заявка не найдена!',
                ]);
            }
            Requests::refuseRequest($request);

            return redirect("/request/{$id}");
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы отказаться от заявки вы должны быть авторизованы
                в качестве сотрудника!',
            ]);
        }
    }

    public function sendComment($id)
    {
        request()->validate(
            [
                'message' => 'required|max:100'
            ],
            [
                'required' => 'Заполните это поле',
                'max' => 'Сообщение не должно превышать 100 символов'
            ]
        );

        $comment = new Comments();
        $comment->sender_id = Auth::user()->id;
        $comment->message = request('message');
        $comment->date = date('Y-m-d H:i:s');
        $comment->request_id = $id;
        $comment->save();
        return redirect("request/{$id}");
    }

    public function answerNo($request_id)
    {
        $request = Requests::find($request_id);
        Users::hideModelWindow($request_id);

        $staff = Users::whereRaw('id = ?', [$request->staff_id])->get()[0];
        $current_negative_reviews = $staff->negative_reviews;
        $staff->negative_reviews = $current_negative_reviews + 1;
        $staff->save();
        return redirect("/request/{$request_id}");

    }

    public function answerYes($request_id)
    {
        $request = Requests::find($request_id);
        Users::hideModelWindow($request_id);

        $staff = Users::whereRaw('id = ?', [$request->staff_id])->get()[0];
        $current_positive_reviews = $staff->positive_reviews;
        $staff->positive_reviews = $current_positive_reviews + 1;
        $staff->save();
        return redirect("/request/{$request_id}");

    }
    public function emptyAnswer($request_id)
    {
        Users::hideModelWindow($request_id);

        return redirect("/request/{$request_id}");

    }

}
