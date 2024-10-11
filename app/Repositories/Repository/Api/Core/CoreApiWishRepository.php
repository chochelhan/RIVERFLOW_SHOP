<?php

namespace App\Repositories\Repository\Api\Core;

use App\Models\Customize\CustomizeWish;


class CoreApiWishRepository {

    protected $wish;

    public function __construct(CustomizeWish $wish) {
        $this->wish = $wish;
        $this->useFields  = $this->wish->useFields;
    }

    public function getWishListByAll(int $user_id) {

        return $this->wish::orderBy('id','desc')->where('user_id',$user_id)->get();
    }
    // pid type user_id 데이타  1개 가져오기
    public function getWishInfoByPidTypeUser(int $pid,string $type,int $userId) {
        return $this->wish::where('pid',$pid)->where('type',$type)->where('user_id',$userId)->first();
    }

    // 해당 pid type 의 전체갯수
    public function getWishTotalByPidType(int $pid,string $type) {
        return $this->wish::where('pid',$pid)->where('type',$type)->count();
    }

    // 저장
    public function insertWish(array $fieldset) {
        return $this->wish::create($fieldset);
    }
    // 삭제
    public function deleteWish(int $id) {
            return $this->wish::destroy($id);
    }


}

