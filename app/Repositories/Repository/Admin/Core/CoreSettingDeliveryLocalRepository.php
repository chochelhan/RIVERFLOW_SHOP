<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Repositories\Repository\Base\BaseSettingDeliveryLocalRepository;

class CoreSettingDeliveryLocalRepository extends BaseSettingDeliveryLocalRepository {

    // 정보 리스트
    public function getDeliveryLocalList() {
        $results = $this->deliveryLocal::orderBy('id','DESC')->get();
        return $results;
    }

    //등록
    public function insertDeliveryLocal(array $params) {
        $insData = $this->deliveryLocal::create($params);
        return $insData->id;
    }

    //수정
    public function updateDeliveryLocal(int $id,array $params) {

        $updData = $this->deliveryLocal::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteDeliveryLocal(int $id) {
        $delData = $this->deliveryLocal::destroy($id);
        return ($delData)?$id:'';
    }



}

