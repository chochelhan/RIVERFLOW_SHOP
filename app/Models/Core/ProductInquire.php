<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class ProductInquire  extends Model {

    public $table;
    protected $fillable = [
        'pid', // 상품 고유키
        'secret', //비밀글여부
        'category', // 카테고리
        'user_id', // 회원고유키
        'name', // 회원명
        'subject', //문의내용
        'status', // 문의상태 wait(대기), complete(완료)
        'content', //답변내용
    ];
    public $useFields = [];
    public $queryParams;

    public function __construct() {
        $this->table = config('tables.productInquire');
        foreach($this->fillable as $key) {
            $this->useFields[$key] = $key;
        }
    }
    public function setQueryParams($params) {
        $this->queryParams = $params;
    }

}
