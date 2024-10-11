<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrderClaim extends Model
{
    public $table;

    protected $fillable = [
        'user_code', // 회원고유코드
        'oid', // 주문고유키
        'oldOstatus', // 클레임 요청이전의 주문상태
        'claimType', // 타입(CR,RR .....)
        'claimMessage', // 내용
        'bankName', // 환불은행명
        'bankOwner', //
        'bankAccount', //
        'recoverPrice',// 환불 금액
        'recoverPoint',// 환불포인트
        'recoverCouponId',// 복구 쿠폰
        'recoverDeliveryPrice',// 반품 배송비
    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.orderClaim');
        foreach($this->fillable as $key) {
            $this->useFields[$key] = $key;
        }
    }

   public function setQueryParams($params) {
        $this->queryParams = $params;
   }

}
