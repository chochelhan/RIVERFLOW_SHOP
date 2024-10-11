<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseProductCategoryRepository;


class CoreProductCategoryRepository extends BaseProductCategoryRepository {

    //등록
    public function insertCategory(array $params) {
        $insData = $this->productCategory::create($params);
        return $insData;
    }

    public function getMaxRankByDepth(int $depth,string $pcode) {
        return $this->productCategory::where('depth',$depth)->where('pcode',$pcode)->max('crank');

    }
    //수정
    public function updateCategory(int $id,array $params) {
        $updData = $this->productCategory::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteCategory(int $id) {
        $row =$this->productCategory::find($id);
        $delData = $this->productCategory::destroy($id);
        if($delData) {
            return ['row'=>$row];
        } else return ['row'=>''];
    }

    //순서변경
    public function sequenceCategoryInfo(array $params) {
        switch($params['cmd']) {
            case 'up':
                $rank = $this->productCategory::where('crank','<',$params['crank'])->where('pcode',$params['pcode'])->where('depth',$params['depth'])->max('crank');
            break;
            case 'down':
                $rank = $this->productCategory::where('crank','>',$params['crank'])->where('pcode',$params['pcode'])->where('depth',$params['depth'])->min('crank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->productCategory::where('crank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }

    // 정보 리스트
    public function getCategoryList() {
        $mainlist = $this->productCategory::where('depth',1)->orderBy('crank','ASC')->get();
        $results = [];
        foreach($mainlist as $mainData) {
            $sublist = $this->productCategory::where('depth',2)->where('pcode',$mainData->code)->orderBy('crank','ASC')->get();
            $subResults = [];
            if(is_object($sublist)) {
                foreach($sublist as $subData) {
                    $subSublist = $this->productCategory::where('depth',3)->where('pcode',$subData->code)->orderBy('crank','ASC')->get();
                    $subSubResults = [];
                    if(is_object($subSublist)) {
                        $subSubResults = $subSublist;
                    }
                    $subData->subList= $subSubResults;
                    $subResults[] = $subData;
                }
            }
            $mainData->subList = $subResults;
            $results[] = $mainData;
        }
        return $results;
    }
    // 상위 코드와 뎁스에 맞는 리스트
    public function getCategoryListByDepth(int $depth,string $pcode) {

        return $this->productCategory::where('pcode',$pcode)->where('depth',$depth)->orderBy('crank','ASC')->get();
    }

}

