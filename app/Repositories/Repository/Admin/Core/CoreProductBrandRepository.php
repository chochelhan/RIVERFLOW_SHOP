<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseProductBrandRepository;

class CoreProductBrandRepository extends BaseProductBrandRepository {


    // 정보 리스트
    public function getBrandList() {
        $results = $this->productBrand::orderBy('brank','ASC')->get();
        return $results;
    }
    //등록
    public function insertBrand(array $fieldsets) {
        $insData = $this->productBrand::create($fieldsets);
        return $insData;
    }

    // 순위 최대값
    public function getMaxRank() {
        return $this->productBrand::max('brank');
    }
    //수정
    public function updateBrand(int $id,array $params) {

        $updData = $this->productBrand::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteBrand(int $id) {
        $row =$this->productBrand::find($id);
        $delData = $this->productBrand::destroy($id);
        return ($delData)?$id:'';
    }

    //순서변경
    public function sequenceBrandInfo(array $params) {

        switch($params['cmd']) {
            case 'up':
                $rank = $this->productBrand::where('brank','<',$params['brank'])->max('brank');
            break;
            case 'down':
                $rank = $this->productBrand::where('brank','>',$params['brank'])->min('brank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->productBrand::where('brank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }



}

