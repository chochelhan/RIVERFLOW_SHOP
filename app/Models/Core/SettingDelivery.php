<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SettingDelivery extends Model {

    public $table = 'setting_delivery';

    protected $fillable = [
        'name',
        'duse', // 사용유무
        'dmethod', // auto(택배/등기/소포) , direct (직접배송)
        'dpriceType', // price(유료 고정배송비), half (조건부 무료) , free(무료)
        'oprice', // 조건부사용일대 기준금액 (이상이면 무료)
        'fprice', // 고정배송비
        'mprice', // 조건부사용일대 oprice 미안일대 배송비
        'localId', // 지역별 배송비 사용일대 지역별 배송비 고유키
        'localUse', // yes 지역별 배송비 사용 ,no 사용안함
        'backPrice', // 환불교환 배송비
        'backAddr', // 환불 교환 주소지
        'dcontent', // 상세내용
        'drank' //순서
    ];

    public $useFields = [];
    public function __construct() {
        $this->table = config('tables.settingDelivery');

        foreach($this->fillable as $key) {
            if($key == 'drank')continue;
            $this->useFields[$key] = $key;
        }
    }


}
