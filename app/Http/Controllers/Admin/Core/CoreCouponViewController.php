<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeCouponService;

class CoreCouponViewController extends Controller
{
    protected $couponService;

    public function __construct(CustomizeCouponService $couponService) {

        $this->couponService = $couponService;
    }
    /**
    * 쿠폰 등록 정보
    **/
    public function getCouponRegistInfo(Request $request) {
        $info = $this->couponService->getCouponRegistInfo($request);
        return restResponse(['status'=>'success','data'=>$info]);
    }

    /**
    * 쿠폰 목록
    **/
    public function getCouponList(Request $request) {
        $info = $this->couponService->getCouponList($request);
        return restResponse(['status'=>'success','data'=>$info]);
    }

    /**
    * 쿠폰 발행 목록
    **/
    public function getCouponPublishList(Request $request) {
        $list = $this->couponService->getCouponPublishList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 쿠폰 발행 목록 (데이타만)
    **/
    public function getCouponPublishDataList(Request $request) {
        $list = $this->couponService->getCouponPublishDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }
}
