<?php

namespace Azuriom\Plugin\Trad\Models;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model {

    protected $table = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'msg_key', 'msg_fr', 'msg_en', 'msg_es', 'msg_de', 'comments'];
    
}
