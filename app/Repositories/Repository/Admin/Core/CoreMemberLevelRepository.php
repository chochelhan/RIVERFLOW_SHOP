<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Repositories\Repository\Base\BaseMemberLevelRepository;


class CoreMemberLevelRepository  extends BaseMemberLevelRepository {


    public function getLevelInfo(int $id) {
        return $this->memberLevel::find($id);

    }

    // 정보 리스트
    public function getLevelList() {
        $results = $this->memberLevel::orderBy('grank','ASC')->get();
        return $results;
    }

    // 사용가능정보 리스트
    public function getLevelUseList() {
        $results = $this->memberLevel::orderBy('grank','ASC')->where('guse','yes')->get();
        return $results;
    }
    //등록
    public function insertLevel(array $fieldsets) {
        $insData = $this->memberLevel::create($fieldsets);
        return $insData;
    }

    // 순위 최대값
    public function getMaxRank() {
        return $this->memberLevel::max('grank');
    }
    //수정
    public function updateLevel(int $id,array $params) {

        $updData = $this->memberLevel::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteLevel(int $id) {
        $row =$this->memberLevel::find($id);
        $delData = $this->memberLevel::destroy($id);
        return ($delData)?$id:'';
    }

    //순서변경
    public function sequenceLevelInfo(array $params) {

        switch($params['cmd']) {
            case 'up':
                $rank = $this->memberLevel::where('grank','<',$params['grank'])->where('gbase','!=','yes')->max('grank');
            break;
            case 'down':
                $rank = $this->memberLevel::where('grank','>',$params['grank'])->where('gbase','!=','yes')->min('grank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->memberLevel::where('grank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }

}

