<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Board  extends Model {

    public $table;
    protected $fillable = [
        'bname',
        'buse',
        'categoryUse',
        'categoryList',
        'wauth',
        'secret',
        'btype',
        'replyUse',
        'rauth',
        'brank'
    ];
    public $useFields = [];
    public $queryParams;

    public function __construct() {
        $this->table = config('tables.board');
        foreach($this->fillable as $key) {
            if($key == 'brank')continue;
            $this->useFields[$key] = $key;
        }
    }
    public function setQueryParams($params) {
        $this->queryParams = $params;
    }

}
