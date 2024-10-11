<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrderClaimProduct extends Model
{
    public $table;

    protected $fillable = [
        'claim_id', // 클레임 테이블 고유키
        'oldOstatus', // 클레임 요청이전의 주문상태
        'opid', // 주문상품 고유키
    ];

    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.orderClaimProduct');
        foreach($this->fillable as $key) {
            $this->useFields[$key] = $key;
        }
    }



}
