<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\ProductBrandRepositoryInterface;
use App\Models\Customize\CustomizeProductBrand;


class BaseProductBrandRepository implements ProductBrandRepositoryInterface {

    protected $productBrand;
    public $useFields;

    public function __construct(CustomizeProductBrand $productBrand) {
        $this->productBrand = $productBrand;
        $this->useFields = $this->productBrand->useFields;
    }


    // 사용가능한 정보 리스트
    public function getBrandUseList() {
        $results = $this->productBrand::where('buse','yes')->orderBy('brank','ASC')->get();
        return $results;
    }
    public function getBrandInfo(int $id) {
        return $this->productBrand::find($id);
    }

}

