<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    public $table;

    protected $fillable = [
        'oid', // 주문고유키
        'opid', // 주문상품 고유키
        'nowstatus', // 현재주문상태
        'oldstatus', // 과거주문상태
        'content',   // 변경 내용
    ];

    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.orderHistory');

        foreach($this->fillable as $key) {
            switch($key) {
                default:
                    $this->useFields[$key] = $key;
                break;
             }
        }
    }



}
