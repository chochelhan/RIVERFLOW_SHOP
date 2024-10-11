<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model {

    use HasApiTokens, HasFactory;

    public $table = '';

    protected $fillable = [
        'name', // 회원명
        'nick', // 닉네임
        'point', //보유포인트
        'lvid', // 등급 (등급 고유키)
        'uid', // 아이디
        'mstatus', // 상태(ing,stop,out)
        'email', //
        'pcs',
        'sns',
        'img',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $queryParams;

    public function setQueryParams($params) {
        $this->queryParams = $params;
    }

     public function __construct() {
            $this->table = config('tables.users');
     }

}
