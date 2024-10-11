<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\ProductCategoryRepositoryInterface;
use App\Models\Customize\CustomizeProductCategory;


class BaseProductCategoryRepository implements ProductCategoryRepositoryInterface {

    protected $productCategory;
    public $useFields;

    public function __construct(CustomizeProductCategory $productCategory) {
        $this->productCategory = $productCategory;
        $this->useFields = $this->productCategory->useFields;

    }
    // 정보 리스트
    public function getCategoryUseList() {
        $mainlist = $this->productCategory::where('depth',1)->where('cuse','yes')->orderBy('crank','ASC')->get();
        $results = [];
        foreach($mainlist as $mainData) {
            $sublist = $this->productCategory::where('depth',2)->where('pcode',$mainData->code)->where('cuse','yes')->orderBy('crank','ASC')->get();
            $subResults = [];
            if(is_object($sublist)) {
                foreach($sublist as $subData) {
                    $subSublist = $this->productCategory::where('depth',3)->where('pcode',$subData->code)->where('cuse','yes')->orderBy('crank','ASC')->get();
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

    // 정보
    public function getCategoryInfo(int $id) {
        return $this->productCategory::find($id);
    }

}

