<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Wish extends Model {

    public $table;

    protected $fillable = [
         'pid', // 타켓팅 id
         'type', // (product(상품),board(게시판),comment(댓글))
         'user_id', //회원고유키
    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.wish');

        foreach($this->fillable as $key) {
            $this->useFields[$key] = $key;
        }
    }
    public function setQueryParams($params) {
        $this->queryParams = $params;
    }
}
