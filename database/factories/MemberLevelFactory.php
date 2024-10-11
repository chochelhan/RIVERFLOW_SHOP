<?php

namespace Database\Factories\Core;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MemberLevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
                         'gname'=>'기본등급',///등급명
                           'guse'=>'yes',  //사용여부
                           'gprice'=>0, // 승급 기준 구매금액
                           'gbase'=>'yes', // 기본 등급 여부
                           'gservicePointUse'=>'no', // 승급시 적립금 지급여부
                           'gservicePoint'=>0, // 승급시 적립금
                           'gpointUse'=>'no', // 구매시 적립금 적립 여부
                           'gpoint'=>0, // 구매시 적립금 적립 %
                           'grank'=>1 // 등급
        ];
    }


}
