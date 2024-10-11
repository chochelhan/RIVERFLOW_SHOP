<?php
namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizeSmsEmail;

class BaseSmsEmailRepository  {

    protected $smsEmail;

    public function __construct(CustomizeSmsEmail $smsEmail) {
        $this->smsEmail = $smsEmail;
    }
    public function getSmsEmailInfoByGid(string $gtype,string $gid) {

        return $this->smsEmail::where('gtype',$gtype)->where('gid',$gid)->first();
    }
}

