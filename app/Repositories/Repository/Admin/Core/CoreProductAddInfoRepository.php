<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Models\Customize\CustomizeProductAddInfo;


class CoreProductAddInfoRepository  {

    protected $productAddInfo;
    public $useFields;

    public function __construct(CustomizeProductAddInfo $productAddInfo) {
        $this->productAddInfo = $productAddInfo;
        $this->useFields = $this->productAddInfo->useFields;
    }


    // 사용가능한 정보 리스트
    public function getAddInfoUseList() {
        $results = $this->productAddInfo::where('buse','yes')->orderBy('brank','ASC')->get();
        return $results;
    }
    public function getAddInfoInfo(int $id) {
        return $this->productAddInfo::find($id);
    }
 // 정보 리스트
    public function getAddInfoList() {
        $results = $this->productAddInfo::orderBy('brank','ASC')->get();
        return $results;
    }
    //등록
    public function insertAddInfo(array $fieldsets) {
        $insData = $this->productAddInfo::create($fieldsets);
        return $insData;
    }

    // 순위 최대값
    public function getMaxRank() {
        return $this->productAddInfo::max('brank');
    }
    //수정
    public function updateAddInfo(int $id,array $params) {

        $updData = $this->productAddInfo::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteAddInfo(int $id) {
        $delData = $this->productAddInfo::destroy($id);
        return ($delData)?$id:'';
    }

    //순서변경
    public function sequenceAddInfo(array $params) {

        switch($params['cmd']) {
            case 'up':
                $rank = $this->productAddInfo::where('brank','<',$params['brank'])->max('brank');
            break;
            case 'down':
                $rank = $this->productAddInfo::where('brank','>',$params['brank'])->min('brank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->productAddInfo::where('brank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }
}

