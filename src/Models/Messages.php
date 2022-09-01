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
    protected $fillable = ['msg_key', 'msg_value', 'msg_suggestion', 'comments', 'suggestionner', 'suggestion_accepter', 'creation'];

    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    public function scopeTable($query, $tableName)
    {
        $query->getQuery()->from = $tableName;
        return $query;
    }
}
