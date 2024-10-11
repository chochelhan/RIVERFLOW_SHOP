<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseProductInquireRepository;


class CoreApiProductInquireRepository extends  BaseProductInquireRepository {


    // 문의 저장
    public function insertProductInquire(array $fieldset) {
        return $this->productInquire::create($fieldset);
    }

    // 문의 목록
    public function getProductInquireList(int $pid) {
        return $this->productInquire::where('pid',$pid)->orderBy('id','desc')->get();
    }

}

