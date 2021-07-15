<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    public $timestamps = false;

    public static function getGroupId($category_id)
    {
        return Groups::whereRaw('category_id = ?', [$category_id])->get()[0]->id;
    }

    public function user()
    {
        return $this->hasMany('App\Users');
    }
    public function category() {
        return $this->belongsTo('App\Categories');
    }
}
