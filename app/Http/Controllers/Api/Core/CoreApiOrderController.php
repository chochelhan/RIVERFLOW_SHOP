<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiOrderService;

/**
* 상품
*
**/
class CoreApiOrderController extends CoreApiAuthHeaderController
{
    protected $orderService;

    public function __construct(Request $request, CustomizeApiOrderService $orderService) {
         parent::__construct($request);
        $this->orderService = $orderService;
    }

    public function insertOrder(Request $request) {

        $data = $this->orderService->insertOrder($request);
        return apiResponse($data,$this->newToken);

    }

    public function orderRegistInfo(Request $request) {
        if(!$request->has(['type','ids'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return restResponse($data);
        }
        $data = $this->orderService->orderRegistInfo($request);
        return apiResponse($data,$this->newToken);

    }

    // 주문시 주문금액 변경(쿠폰,적립금,주소변경에 따른...)
    public function updateOrderPriceInfo(Request $request) {
        if(!$request->has(['type','ids'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return restResponse($data);
        }
        $data = $this->orderService->updateOrderPriceInfo($request);
        return apiResponse($data,$this->newToken);

    }

    // 주문완료
    public function getOrderComplete(Request $request) {

        $data = $this->orderService->getOrderComplete($request);
        return apiResponse($data,$this->newToken);

    }

}
