<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberLevel extends Model
{
    use HasFactory;
    public $table;

    protected $fillable = [
        'gname', //등급명
        'gdesc', // 등급설명
        'guse',  //사용여부
        'gprice', // 승급 기준 구매금액
        'gbase', // 기본 등급 여부
        'gservicePointUse', // 승급시 적립금 지급여부
        'gservicePoint', // 승급시 적립금
        'gpointUse', // 구매시 적립금 적립 여부
        'gpoint', // 구매시 적립금 적립 %
        'gcoupon', // 쿠폰
        'grank' // 등급
    ];
    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.memberLevel');
        foreach($this->fillable as $key) {
            if($key=='grank' || $key=='gbase')continue;
            $this->useFields[$key] = $key;
        }
    }

}
