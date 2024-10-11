<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Point  extends Model {

    public $table;
    protected $fillable = [
        'user_id', // 회원 고유키
        'ptype', // plus, minus
        'point', // 포인트
        'oid', // 주문고유키
        'pointMsg', // 메세지
        'pcode' // 적립 구분 ()
    ];
    public $useFields = [];
    public $queryParams;
    public $PCODES = [
                'direct'=>'관리자 직접적립',
                'order'=>'상품구매시 사용',
                'ADC'=>'관리자가 주문상태를 구매확정에서 배송완료로 변경',
                'join'=>'회원가입 적립',
                'review'=>'구매후기 적립',
                'CC'=>'주문취소에 의한 복구',
                'OC'=>'구매확정에 의한 적립',
                'RC'=>'반품완료에 의한 복구',
    ];
    public function __construct() {
        $this->table = config('tables.point');

        foreach($this->fillable as $key) {
            switch($key) {
                //case 'oid':
                //case 'pointMsg':
                //case 'pcode':
                  //  continue;
                //break;
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
