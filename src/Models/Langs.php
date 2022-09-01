<?php

namespace Azuriom\Plugin\Trad\Models;

use Illuminate\Database\Eloquent\Model;

class Langs extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'lang_key', 'lang_name'];

}
