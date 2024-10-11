<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


            $memberTable = config('tables.users');
            $memberParams = ['uid' =>'admin',
                    'name' => '관리자',
                    'email' => 'admin@sample.com',
                    'email_verified_at' => now(),
                    'admin'=>'yes',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10)];
            DB::table($memberTable)->insert($memberParams);

            $memberLevelTable = config('tables.memberLevel');
            $memberLevelParams = ['gname'=>'기본등급',///등급명
                                    'guse'=>'yes',  //사용여부
                                    'gprice'=>0, // 승급 기준 구매금액
                                    'gbase'=>'yes', // 기본 등급 여부
                                    'gservicePointUse'=>'no', // 승급시 적립금 지급여부
                                    'gservicePoint'=>0, // 승급시 적립금
                                    'gpointUse'=>'no', // 구매시 적립금 적립 여부
                                    'gpoint'=>0, // 구매시 적립금 적립 %
                                    'grank'=>1]; // 등급
             DB::table($memberLevelTable)->insert($memberLevelParams);

            $boardTable = config('tables.board');


            $boardParams = ['bname'=>'FAQ',
                                  'buse'=>'yes',
                                  'categoryUse'=>'yes',
                                  'wauth'=>'admin',
                                  'secret'=>'no',
                                  'btype'=>'faq',
                                  'replyUse'=>'no',
                                  'rauth'=>'no',
                                  'brank'=>1];

             DB::table($boardTable)->insert($boardParams);

             $boardParams = ['bname'=>'공지사항',
                                               'buse'=>'yes',
                                               'categoryUse'=>'no',
                                               'wauth'=>'admin',
                                               'secret'=>'no',
                                               'btype'=>'notice',
                                               'replyUse'=>'no',
                                               'rauth'=>'no',
                                               'brank'=>2];

            DB::table($boardTable)->insert($boardParams);
    }
}
