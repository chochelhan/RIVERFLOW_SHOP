<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Shipping  extends Model {

    public $table;
    protected $fillable = [
        'user_id', // 회원정보 고유키
        'title', // 제목
        'rname', // 배송받는사람이름
        'rpcs', // 배송지 전화번호
        'rpost',  // 배송지 우편번호
        'raddr1', // 배송지 주소1
        'raddr2', // 배송지 주소2
        'jibunAddr', // 지번주소 (지역별 배송지 검색시)
        'defaultShipping', // 기본배송지

    ];
    public $useFields = [];
    public function __construct() {
        $this->table = config('tables.shipping');
        foreach($this->fillable as $key) {
            if($key!='user_id')$this->useFields[$key] = $key;
        }
    }


}
