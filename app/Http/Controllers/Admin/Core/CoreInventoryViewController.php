<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeInventoryService;


/**
* 재고
*
**/
class CoreInventoryViewController extends Controller
{
    protected $inventoryService;


    public function __construct(CustomizeInventoryService $inventoryService) {
        $this->inventoryService = $inventoryService;

    }

    /**
    * 미옵션상품 재고 목록
    **/
    public function getProductList(Request $request) {
        $list = $this->inventoryService ->getProductList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 미옵션상품  재고 데이타 목록
    **/
    public function getProductDataList(Request $request) {
        $list = $this->inventoryService ->getProductDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 옵션상품 재고 목록
    **/
    public function getOptionList(Request $request) {
        $list = $this->inventoryService ->getOptionList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 옵션상품  재고 데이타 목록
    **/
    public function getOptionDataList(Request $request) {
        $list = $this->inventoryService ->getOptionDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 재고 히스토리 목록
    **/
    public function getHistoryList(Request $request) {
        $list = $this->inventoryService ->getInventoryHistoryList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }


}
