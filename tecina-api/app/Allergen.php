<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    public function getTranslate() {
        return $this->belongsToMany('App\language', 'allergen_translations')->withPivot('name', 'description');
    }


}
