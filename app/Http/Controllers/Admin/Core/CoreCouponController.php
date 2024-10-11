<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeCouponService;

class CoreCouponController extends Controller
{
    protected $couponService;
    public function __construct(CustomizeCouponService $couponService) {

        $this->couponService = $couponService;

    }

    /*** 쿠폰 저장 ***/
    public function insertCoupon(Request $request) {
        $data = $this->couponService->insertCoupon($request);

        return restResponse($data);
    }

    /*** 쿠폰 수정 ***/
    public function updateCoupon(Request $request) {
        $data = $this->couponService->updateCoupon($request);
        return restResponse($data);

    }

    /*** 쿠폰삭제 **/
    public function deleteCoupon(Request $request) {
        //$data = $this->deliveryService->deleteDelivery($request);
       //return restResponse($data);

    }


}
