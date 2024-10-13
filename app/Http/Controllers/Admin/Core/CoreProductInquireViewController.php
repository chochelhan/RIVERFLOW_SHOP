<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeProductInquireService;
use Illuminate\Http\response;

class CoreProductInquireViewController extends Controller
{
    protected $productInquireService;
    public function __construct(CustomizeProductInquireService $productInquireService) {
        $this->productInquireService = $productInquireService;

    }

    /**
    * 상품문의 목록
    **/
    public function getInquireList(Request $request) {
        $list = $this->productInquireService ->getInquireList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 상품문의 데이타 목록
    **/
    public function getInquireDataList(Request $request) {
        $list = $this->productInquireService ->getInquireDataList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 상품문의 정보
    **/
    public function getInquireInfo(Request $request) {
        $info = $this->productInquireService->getInquireInfo($request);
        return response()->json(['status' => 'success','data'=>$info]);
    }
}
