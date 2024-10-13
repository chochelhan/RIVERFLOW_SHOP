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
class CoreOrderViewController extends Controller
{
    protected $orderService;


    public function __construct(CustomizeOrderService $orderService) {
        $this->orderService = $orderService;
    }

    /**
    * 주문 목록
    **/
    public function getOrderList(Request $request) {
        $list = $this->orderService ->getOrderList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 주문 데이타 목록
    **/
    public function getOrderDataList(Request $request) {
        $list = $this->orderService ->getOrderDataList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 주문 상세
    **/
    public function getOrderDetail(Request $request) {
        $list = $this->orderService ->getOrderDetail($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 취소 목록
    **/
    public function getCancleList(Request $request) {
        $list = $this->orderService ->getCancleList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 취소 데이타 목록
    **/
    public function getCancleDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'cancle');
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 반품 목록
    **/
    public function getReturnList(Request $request) {
        $list = $this->orderService ->getReturnList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 반품 데이타 목록
    **/
    public function getReturnDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'return');
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 교환 목록
    **/
    public function getExchangeList(Request $request) {
        $list = $this->orderService ->getExchangeList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 교환 데이타 목록
    **/
    public function getExchangeDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'exchange');
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 환불 목록
    **/
    public function getRefundList(Request $request) {
        $list = $this->orderService ->getRefundList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 환불 데이타 목록
    **/
    public function getRefundDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'refund');
        return response()->json(['status' => 'success','data'=>$list]);
    }

}
