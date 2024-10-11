<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model {

    public $table;

    protected $fillable = [
        'pid', // 상품고유키
        'optionUse', // 옵션사용여부
        'oid', // 옵션고윺키
        'disable_amt', // 판매불가능 재고
        'able_amt', // 판매가능한 재고
        'total_amt', // 총 재고
        'sale_amt', // 판매 갯수
        'manger_code', // 관리코드
    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.productInventory');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'disable_amt':
                case 'sale_amt':
                case 'manger_code':
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
