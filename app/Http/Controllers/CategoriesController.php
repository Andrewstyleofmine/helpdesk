<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Groups;
use App\Requests;
use App\Users;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function showCategories()
    {
        if (Users::isAdmin()) {
            $categories = Categories::paginate(3);
            return view('categories', ['categories' => $categories]);
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы просмотреть список категорий вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
    }

    public function addCategory()
    {
        request()->validate(
            [
                'title' => 'required',
            ],
            [
                'required' => 'Заполните это поле',
            ]
        );
        if (Users::isAdmin()) {
            Categories::addCategory(request('title'));
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы добавить категорию вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
        return redirect('/categories');
    }

    public function deleteCategory($id)
    {
        if (Users::isAdmin()) {
            Categories::deleteCategory(Categories::find($id));
        } else {
            return view('error', [
                'title' => '403',
                'message' => 'Чтобы удалить категорию вы должны быть авторизованы
                в качестве администратора!',
            ]);
        }
        return redirect('/categories');
    }
}
