<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\Customize\CustomizeOrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\response;

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
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /**
    * 클레임 상태 변경
    **/
    public function updateClaimStatus(Request $request) {
        $data = $this->orderService ->updateClaimStatus($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /**
    * 환불처리
    **/
    public function activeRefund(Request $request) {
        $data = $this->orderService ->activeRefund($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

}
