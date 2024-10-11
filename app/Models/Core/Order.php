<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $table;

    protected $fillable = [
        'is_member', // 회원여부 ('yes',no)
        'user_code', // 회원인경우 회원고유키, 비회원인경우 세션코드를 넣는다
        'order_code', // 주문번호
        'pg_code', // pg사 주문코드
        'oamt', // 구매수량
        'price', // 주문금액
        'paymethod', // 결제방법
        'payInfo', // 결제정보
        'ostatus', // 주문상태 (notpay('미입금'),income(입금)
        'oname',// 주문자명
        'opcs', // 주문자 전화번호
         'oemail', // 주문자 이메일
         'rname', // 배송받는사람이름
         'rpcs', // 배송지 전화번호
         'rpost',  // 배송지 우편번호
         'raddr1', // 배송지 주소1
         'raddr2', // 배송지 주소2
         'rmessage', // 배송 메세지
         'deliveryId', //배송비 고유키
         'deliveryPrice',// 배송비
         'localDeliveryPrice',// 추가배송비
         'usePoint', // 사용적립금
         'useCouponId', // 사용 쿠폰 아이디
         'useCouponPrice', // 사용 쿠폰금액
         'reservePoint', // 받을적립금
         'deliveryCompany', // 배송업체 코드
         'sendNumber', // 송장번호
         'deliverTracker', // 배송추적 데이타

    ];

    public $queryParams = [];
    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.order');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'is_member':
                case 'user_code':
                case 'order_code':
                case 'pg_code':
                case 'payInfo':
                case 'ostatus':
                case 'usePoint':
                case 'reservePoint':
                case 'localDeliveryPrice':// 추가배송비
                case 'useCouponId': // 사용 쿠폰 아이디
                case 'useCouponPrice': // 사용 쿠폰금액
                case 'deliveryCompany':
                case 'sendNumber':
                case 'deliverTracker':

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
