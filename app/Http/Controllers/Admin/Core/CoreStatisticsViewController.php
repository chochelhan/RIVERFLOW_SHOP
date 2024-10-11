<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeOrderStatisticsService;

class CoreStatisticsViewController extends Controller
{
    protected $orderStatisticsService;

    public function __construct(CustomizeOrderStatisticsService $orderStatisticsService) {
        $this->orderStatisticsService = $orderStatisticsService;

    }

    /**
    * 회원가입 통계
    **/
    public function getJoinMember(Request $request) {

        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }
        $list = $this->orderStatisticsService->getJoinMemberStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }
    /**
    * 전체 주문 통계
    **/
    public function getOrder(Request $request) {

        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }
        $list = $this->orderStatisticsService->getOrderStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);

    }
    /**
    * 회원별 주문 통계
    **/
    public function getOrderMember(Request $request) {

        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }
        $list = $this->orderStatisticsService->getOrderMemberStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);

    }
    /**
    * 회원별 주문 통계 (데이타)
    **/
    public function getOrderMemberDataList(Request $request) {
        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }

        $list = $this->orderStatisticsService->getOrderMemberDataStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }
    /**
    * 상품별 주문 통계
    **/
    public function getOrderProduct(Request $request) {

        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }
        $list = $this->orderStatisticsService->getOrderProductStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);

    }
    /**
    * 상품별 주문 통계 (데이타)
    **/
    public function getOrderProductDataList(Request $request) {
        $checkParams = $this->checkOrderRequest($request);
        if($checkParams) {
            return $checkParams;
        }

        $list = $this->orderStatisticsService->getOrderProductDataStatistics($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 공통 인풋값 체크
    **/
    private function checkOrderRequest(Request $request) {
        if(!$request->has('dateType')) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        switch($request->input('dateType')) {
            case 'day':
                if(!$request->has('month')) {
                     return restResponse(['status'=>'emptyField','data'=>'']);
                }
                break;
            default:
                if(!$request->has(['styear','enyear'])) {
                    return restResponse(['status'=>'emptyField','data'=>'']);
                }
                break;
        }
        return '';

    }
}
