<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeOrderReviewService;


class CoreOrderReviewViewController extends Controller
{
    protected $orderReviewService;
    public function __construct(CustomizeOrderReviewService $orderReviewService) {
        $this->orderReviewService = $orderReviewService;

    }

    /**
    * (구매)후기 목록
    **/
    public function getReviewList(Request $request) {
        $list = $this->orderReviewService ->getReviewList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * (구매)후기 데이타 목록
    **/
    public function getReviewDataList(Request $request) {
        $list = $this->orderReviewService ->getReviewDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * (구매)후기 정보
    **/
    public function getReviewInfo(Request $request) {
        $info = $this->orderReviewService->getReviewInfo($request);
        return restResponse(['status'=>'success','data'=>$info]);
    }
}
