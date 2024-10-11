<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\Customize\CustomizeOrderService;
use Illuminate\Support\Facades\Auth;


/**
* 주문
*
**/
class CoreOrderController extends Controller
{
    protected $orderService;


    public function __construct(CustomizeOrderService $orderService) {
        $this->orderService = $orderService;
    }

    /**
    * 주문 상태 변경
    **/
    public function updateOrderStatus(Request $request) {
        $data = $this->orderService ->updateOrderStatus($request);
        return restResponse($data);
    }

    /**
    * 클레임 상태 변경
    **/
    public function updateClaimStatus(Request $request) {
        $data = $this->orderService ->updateClaimStatus($request);
        return restResponse($data);
    }

    /**
    * 환불처리
    **/
    public function activeRefund(Request $request) {
        $data = $this->orderService ->activeRefund($request);
        return restResponse($data);
    }

}
