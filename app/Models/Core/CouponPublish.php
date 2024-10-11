<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class CouponPublish  extends Model {

     public $table;

     protected $fillable = [
          'cid',
          'user_id', // 회원 고유키
          'expireStdate', // 사용기간 시작
          'expireEndate', // 사용기간 종료일
          'ctype',   //혜택구분 product(상품),basket(장바구니)
          'cname',
          'pubtype',  // 발급유형 code(난수코드), direct(관리자 직접발급)
          'publish_code', // 난수코드 발행시 코드
          'cuse', // 사용여부
          'couponMsg', // 지급메세지
          'gtype', // 지급 타입
          'discountType', // 할인 타입 fix(금액),rate(정률 %)
          'discountPrice', // 할인금액
          'discountRate', // 할인 %
          'discountRatePrice', //정률시 최대 할인 금액

     ];
     public $useFields = [];
     public $queryParams = [];
     public $GTYPES = ['direct'=>'관리자 지급','code'=>'코드번호 입력'];

     public function __construct() {
        $this->table = config('tables.couponPublish');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'cuse':
                case 'publish_code':
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
