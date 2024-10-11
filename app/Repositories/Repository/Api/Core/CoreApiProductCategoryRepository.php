<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseProductCategoryRepository;


class CoreApiProductCategoryRepository extends BaseProductCategoryRepository {


    // 정보 리스트
    public function getCategoryUseListByParent(int $id) {
        $info = $this->getCategoryInfo($id);
        if(!$info)return false;

        $mainlist = $this->productCategory::where('depth',$info->depth+1)->where('pcode',$info->code)->where('cuse','yes')->orderBy('crank','ASC')->get();
        $results = [];
        foreach($mainlist as $mainData) {
            $sublist = $this->productCategory::where('depth',$mainData->depth+1)->where('pcode',$mainData->code)->where('cuse','yes')->orderBy('crank','ASC')->get();
            $mainData->subList = $sublist;
            $results[] = $mainData;
        }
        return [$info,$results];
    }
}

