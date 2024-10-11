<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public $table;

    protected $fillable = [
        'oid', // 주문고유키
        'pid', // 상품고유키
        'user_id', // 회원고유키
        'pname',
        'serviceType',
        'brandId',
        'couponId', // 상품쿠폰 사용일때 쿠폰 pub id
        'pcode',
        'category1',
        'category2',
        'category3',
        'opt_name', //옵션명
        'opt_id', //옵션고유키
        'opt_code', //옵션관리코드
        'price',
        'dcprice',
        'payprice', // 최종구매금액
        'oamt', //구매수량
        'tempInvAmt', // 임시 저장될 상품 현재재고
        'listImg', //상품목록이미지
        'ostatus',// 주문상태 (notpay('미입금'),income(입금)
        'optionSingleInfos', // 단독형 옵션정보
        'claimDate', // 클레임일자

    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.orderProduct');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'oid':
                case 'opt_name':
                case 'opt_code':
                case 'oamt':
                case 'ostatus':
                case 'opt_id':
                case 'claimDate':
                case 'tempInvAmt':
                case 'optionSingleInfos':
                case 'couponId':
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
