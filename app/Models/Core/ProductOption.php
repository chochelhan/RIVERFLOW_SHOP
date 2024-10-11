<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    public $table;

    protected $fillable = [
       'pid', // 상품고유키
       'option_name', //옵션명(대분류)
       'option_code', // 옵션코드
       'name',  // 옵션명
       'price',  // 가격
       'dcprice', // 즉시 할인가
       'amt', // 재고
       'add_amt', // 추가재고
       'ouse', // 사용여부 (Y,N)
       'manger_code', // 재고관리코드
       'orequired', // 필수옵션 여부 (Y,N)
    ];

    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.productOption');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'add_amt':
                    continue;
                break;
                default:
                    $this->useFields[$key] = $key;
                break;
             }
        }
    }



}
