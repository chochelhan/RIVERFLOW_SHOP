<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $table;

    protected $fillable = [
        'user_code', // 회원코드
        'ctype', // 직접주문시 임시저장 temp, 기본 base
        'pid', // 상품고유키
        'camt', // 주문수량
        'option_id',//옵션고유키
        'singleOptionInfos', // 단독형 옵션정보
    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.cart');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'user_code':
                case 'ctype':
                case 'singleOptionInfos':
                    continue;
                break;
                default:
                    $this->useFields[$key] = $key;
                break;
             }
        }
    }
    public function setQueryParams($params) {
        $this->queryParams = $params;
    }



}
