<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrderReview  extends Model {

    public $table;
    protected $fillable = [
        'oid', // 주문고유키
        'pid', // 상품 고유키
        'content', // 내용
        'grade', // 평점
        'imgs', // 이미지
        'point', // 받을 적립금
        'user_id', // 회원고유키
        'commentCnt', // 댓글수
        'is_delete', // 삭제여부 (관리자 삭제시 블라인드 처리용)
    ];
    public $useFields = [];
    public $queryParams;
    public function __construct() {
        $this->table = config('tables.orderReview');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'is_delete':
                case 'commentCnt':
                case 'imgs':
                case 'point':
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
