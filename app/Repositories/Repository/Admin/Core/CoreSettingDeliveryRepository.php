<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Repositories\Repository\Base\BaseSettingDeliveryRepository;

class CoreSettingDeliveryRepository extends BaseSettingDeliveryRepository {

    // 정보 리스트
    public function getDeliveryList() {
        $results = $this->delivery::orderBy('drank','ASC')->get();
        return $results;
    }

    // 사용설정된 정보만 가져옴
    public function getDeliveryUseList() {
        $results = $this->delivery::where('duse','yes')->orderBy('drank','ASC')->get();
        return $results;
    }
    public function getMaxRank() {
        return $this->delivery::max('drank');
    }
    //등록
    public function insertDelivery(array $params) {
        $insData = $this->delivery::create($params);
        return $insData;
    }

    //수정
    public function updateDelivery(int $id,array $params) {

        $updData = $this->delivery::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteDelivery(int $id) {
        $delData = $this->delivery::destroy($id);
        return ($delData)?$id:'';
    }
    //순서 정보 가져오기
    public function getSequenceDeliveryRank(array $params) {
        switch($params['cmd']) {
            case 'up':
                $rank = $this->delivery::where('drank','<',$params['drank'])->max('drank');
            break;
            case 'down':
                $rank = $this->delivery::where('drank','>',$params['drank'])->min('drank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->delivery::where('drank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }




}

