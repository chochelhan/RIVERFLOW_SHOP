<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseMemberRepository;


class CoreApiMemberRepository extends  BaseMemberRepository {

    public function insertMember(array $fieldsets) {
        $insData = $this->member::create($fieldsets);
        return $insData;
    }



    public function checkUid(string $uid) {
        return $this->member::where('uid',$uid)->first();
    }

    public function checkNick(string $nick) {
        return $this->member::where('nick',$nick)->first();
    }

    public function checkMyNick(int $id,string $nick) {
        return $this->member::where('nick',$nick)->where('id','!=',$id)->first();
    }

}

