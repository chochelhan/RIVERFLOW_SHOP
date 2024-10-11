<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Repositories\Repository\Base\BaseSmsEmailRepository;

class CoreSmsEmailRepository extends BaseSmsEmailRepository {


    public function getSmsEmailInfoByGtype(string $gtype) {

        return $this->smsEmail::where('gtype',$gtype)->get();
    }

    //등록
    public function insertSmsEmailSetting(array $params) {
        $insData = $this->smsEmail::create($params);
        return $insData;
    }

    //수정
    public function updateSmsEmailSetting(int $id,array $params) {

        $updData = $this->smsEmail::find($id)->update($params);
        return $updData;
    }

}
