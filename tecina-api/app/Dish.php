<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ingredients'
    ];


    public function getTranslate() {
        return $this->belongsToMany('App\language', 'dishes_translations')->withPivot('name', 'description');
    }
}
