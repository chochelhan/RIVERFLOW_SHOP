<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Coupon  extends Model {

     public $table;

     protected $fillable = [
          'pubStdate',
          'pubEndate',
          'expireStdate',
          'expireEndate',
          'ctype', //혜택구분 product(상품),basket(장바구니)
          'cname',
          'camt', // 쿠폰발행수량
          'pubtype', // 발급유형 code(난수코드), direct(관리자 직접발급)
          'mlevel',
          'mlimit', // 1인당 최대 지급수 if pubtype== code
          'minPriceUse', // 사용시 최소금액 사용여부
          'minPrice',
          'maxPriceUse', // 사용시 최대 금액 사용여부
          'maxPrice',
          'discountType', // 할인 타입 fix(금액),rate(정률 %)
          'discountPrice', // 할인금액
          'discountRate', // 할인 %
          'discountRatePrice', //정률시 최대 할인 금액
          'pointDeny', // 포인트 동시 사용여부
          'ptType', // 해당되는 상품 all (전체) , single (개별지정) , brand (브랜드) ,category (카테고리)
          'ptInData', //대상 상품
          'ptOutData', // 제외대상 상품
          'ptDeny', // 할인 제외대상 사용여부 yes or no
          'cplatform',
          'useExpireType', // 사용가능기간 구분 after(발급일로 부터 ), date(특정일 지정)
          'afterDay', // 발급일로 부터 ~일까기

     ];
     public $useFields = [];
     public $queryParams = [];

     public function __construct() {
       $this->table = config('tables.coupon');
        foreach($this->fillable as $key) {
            switch($key) {
                case 'ptInData':
                case 'ptOutData':
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
