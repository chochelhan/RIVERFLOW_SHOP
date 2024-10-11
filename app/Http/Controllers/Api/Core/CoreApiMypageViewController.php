<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiMypageService;
use App\Services\Api\Customize\CustomizeApiReviewService;
use App\Services\Api\Customize\CustomizeApiMyShippingService;
use App\Services\Api\Customize\CustomizeApiMyCouponService;
use App\Services\Api\Customize\CustomizeApiDeliveryTrackerService;



class CoreApiMypageViewController extends CoreApiAuthHeaderController
{
    protected $mypageService;
    protected $reviewService;
    protected $shippingService;
    protected $couponService;
    protected $deliveryTrackerService;

    public function __construct(Request $request,CustomizeApiMypageService $mypageService,
                                CustomizeApiReviewService $reviewService,
                                CustomizeApiMyShippingService $shippingService,
                                CustomizeApiDeliveryTrackerService $deliveryTrackerService,
                                CustomizeApiMyCouponService $couponService) {

        parent::__construct($request);

        $this->mypageService = $mypageService;
        $this->reviewService = $reviewService;
        $this->shippingService = $shippingService;
        $this->couponService = $couponService;
        $this->deliveryTrackerService = $deliveryTrackerService;

    }

    public function getMemberLevelName() {
        $data = $this->mypageService->getMemberData();
        return apiResponse($data,$this->newToken);
    }
    // 메인
    public function getMyMain(Request $request) {

        $data = $this->mypageService->getMyMain($request);
        return apiResponse($data,$this->newToken);
    }

    public function getMemberInfo() {
        $data = $this->mypageService->getMemberInfo();
        return apiResponse($data,$this->newToken);
    }

    // 주문내역
    public function getOrderList(Request $request) {

        $data = $this->mypageService->getOrderList($request);
        return apiResponse($data,$this->newToken);
    }


    // 주문상세
    public function getMyOrderDetail(Request $request) {
        if(!$request->has('id')) {
            $data = ['status'=>'emptyField','data'=>''];
            return restResponse($data);
        }
        $data = $this->mypageService->getOrderDatail($request);
        return apiResponse($data,$this->newToken);
    }

    // 주문 상품 목록
    public function getOrderProductList(Request $request) {

        $data = $this->mypageService->getOrderProductList($request);
        return apiResponse($data,$this->newToken);
    }

    // 적립금 목록
    public function getMyPointList(Request $request) {

        $data = $this->mypageService->getPointList($request);
        return apiResponse($data,$this->newToken);
    }

    //  리뷰 등록가능한 주문목록(구매확정)
    public function getMyAbleReviewOrderList(Request $request) {

        $data = $this->reviewService->getMyAbleReviewOrderList($request);
        return apiResponse($data,$this->newToken);
    }
    //  리뷰 목록
    public function getMyReviewList(Request $request) {

        $data = $this->reviewService->getMyReviewList($request);
        return apiResponse($data,$this->newToken);
    }

    //  리뷰 정보
    public function getMyReviewInfo(Request $request) {

        $data = $this->reviewService->getMyReviewInfo($request);
        return apiResponse($data,$this->newToken);
    }

    //  배송지 목록
    public function getMyShippingList() {

        $data = $this->shippingService->getMyShippingList();
        return apiResponse($data,$this->newToken);
    }
    //  배송지 목록
    public function getMyShippingInfo(Request $request) {
        $data = $this->shippingService->getMyShippingInfo($request);
        return apiResponse($data,$this->newToken);
    }

    //  배송조회
    public function getDeliveryTracker(Request $request) {

        $data = $this->deliveryTrackerService->getDeliveryTracker($request);
        return apiResponse($data,$this->newToken);
    }

    //  쿠폰 목록
    public function getMyCouponList() {

        $data = $this->couponService->getMyCouponList();
        return apiResponse($data,$this->newToken);
    }
    public function getClaimCheckProductList(Request $request) {
        if(!$request->has(['oid','claimType'])) {
             $data = ['status'=>'emptyField','data'=>''];
             return restResponse($data);
        }
        $data = $this->mypageService->getClaimCheckProductList($request);
        return apiResponse($data,$this->newToken);
    }

}
