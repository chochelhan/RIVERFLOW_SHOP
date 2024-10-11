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
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 주문 데이타 목록
    **/
    public function getOrderDataList(Request $request) {
        $list = $this->orderService ->getOrderDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 주문 상세
    **/
    public function getOrderDetail(Request $request) {
        $list = $this->orderService ->getOrderDetail($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 취소 목록
    **/
    public function getCancleList(Request $request) {
        $list = $this->orderService ->getCancleList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 취소 데이타 목록
    **/
    public function getCancleDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'cancle');
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 반품 목록
    **/
    public function getReturnList(Request $request) {
        $list = $this->orderService ->getReturnList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 반품 데이타 목록
    **/
    public function getReturnDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'return');
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 교환 목록
    **/
    public function getExchangeList(Request $request) {
        $list = $this->orderService ->getExchangeList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 교환 데이타 목록
    **/
    public function getExchangeDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'exchange');
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 환불 목록
    **/
    public function getRefundList(Request $request) {
        $list = $this->orderService ->getRefundList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 환불 데이타 목록
    **/
    public function getRefundDataList(Request $request) {
        $list = $this->orderService ->getClaimDataList($request,'refund');
        return restResponse(['status'=>'success','data'=>$list]);
    }

}
