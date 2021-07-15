<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    public $timestamps = false;

    public static function getCategoryName($id)
    {
        return Categories::find($id)->title;
    }

    public static function getCategoryId($title)
    {
        if ($title == 'Не указано') {
            return null;
        }
        return Categories::whereRaw("title = ?", [$title])->get()[0]->id;
    }

    public function group()
    {
        return $this->belongsTo('App\Groups');
    }

    public static function addCategory($title)
    {
        $category = new Categories();
        $group = new Groups();

        $category->title = $title;
        $category->save();

        $group->category_id = $category->id;
        $group->save();

        $category->group_id = $group->id;
        $category->save();
    }

    public static function deleteCategory($category)
    {
        $group = $category->group;
        $category->delete();
        $group->delete();
    }
}
