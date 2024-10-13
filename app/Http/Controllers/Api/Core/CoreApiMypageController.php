<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiMypageService;
use App\Services\Api\Customize\CustomizeApiReviewService;
use App\Services\Api\Customize\CustomizeApiMemberService;
use App\Services\Api\Customize\CustomizeApiMyShippingService;

/**
* 회원
*
**/
class CoreApiMypageController extends CoreApiAuthHeaderController
{

    protected $mypageService;
    protected $reviewService;
    protected $memberService;
    protected $shippingService;


    public function __construct(Request $request,CustomizeApiMypageService $mypageService,
                                CustomizeApiReviewService $reviewService,
                                CustomizeApiMemberService $memberService,
                                CustomizeApiMyShippingService $shippingService) {
        parent::__construct($request);
        $this->mypageService = $mypageService;
        $this->reviewService = $reviewService;
        $this->memberService = $memberService;
        $this->shippingService = $shippingService;

    }

    /*** 클레임정보 저장 ***/
    public function insertOrderClaim(Request $request) {
        if (!$request->has(['oid','claimType','opIds'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->mypageService->insertOrderClaim($request);
        return $this->apiResponse($data,$this->newToken);
    }


    /*** 클레임정보 저장 ***/
    public function updateOrderComplete(Request $request) {
        if (!$request->has('id')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->mypageService->updateOrderComplete($request);
        return $this->apiResponse($data,$this->newToken);
    }

    /*** 구매후기 저장 ***/
    public function insertMyOrderReview(Request $request) {


        $data = $this->reviewService->insertOrderReview($request);
        return $this->apiResponse($data,$this->newToken);
    }

    /*** 회원정보 // 닉네임체크 ***/
    public function checkMemberNick(Request $request) {
        $data = $this->memberService->checkMyMemberNick($request);
        return $this->apiResponse($data,$this->newToken);
    }

    /*** 회원정보 // 정보변경 ***/
    public function updateMemberInfo(Request $request) {
        $data = $this->memberService->updateMemberInfo($request);
        return $this->apiResponse($data,$this->newToken);
    }


    /*** 회원정보 // 이미지변경 ***/
    public function updateMemberImage(Request $request) {
        $data = $this->memberService->updateMemberImage($request);
        return $this->apiResponse($data,$this->newToken);
    }

    /*** 배송지 // 등록/수정 ***/
    public function updateMyShipping(Request $request) {
        $data = $this->shippingService->updateMyShipping($request);
        return $this->apiResponse($data,$this->newToken);
    }
    /*** 배송지 // 삭제 ***/
    public function deleteMyShipping(Request $request) {
        $data = $this->shippingService->deleteMyShipping($request);
        return $this->apiResponse($data,$this->newToken);
    }

}
