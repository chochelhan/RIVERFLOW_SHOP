<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeOrderStatisticsRepository;


use Illuminate\Http\Request;

class CoreOrderStatisticsService  {

    protected $orderStatisticsRepository;

    public function __construct(CustomizeOrderStatisticsRepository $orderStatisticsRepository) {

        $this->orderStatisticsRepository = $orderStatisticsRepository;

    }

    // 회원가입 통계
    public function getJoinMemberStatistics(Request $request) {
        return $this->orderStatisticsRepository->getMemberStatistics($request->all());
    }
    // 전체 주문 통계
    public function getOrderStatistics(Request $params) {

        $requestParams = $params->all();
        $data = $this->orderStatisticsRepository->getOrderStatistics($requestParams);

        return $data;

    }
    // 회원별 주문통계
    public function getOrderMemberStatistics(Request $params) {

        $requestParams = $params->all();
        $data['memberList'] = $this->orderStatisticsRepository->getMemberList();
        $data['list'] = $this->orderStatisticsRepository->getOrderMemberStatistics($requestParams);

        return $data;

    }
    // 회원별 주문통계(데이타만)
    public function getOrderMemberDataStatistics(Request $params) {

        $requestParams = $params->all();
        $data = $this->orderStatisticsRepository->getOrderMemberStatistics($requestParams);

        return $data;

    }
    // 상품별 주문통계
    public function getOrderProductStatistics(Request $params) {

        $requestParams = $params->all();
        $data['productList'] = $this->orderStatisticsRepository->getProductList();
        $data['list'] = $this->orderStatisticsRepository->getOrderProductStatistics($requestParams);

        return $data;

    }
    // 상품별 주문통계(데이타만)
    public function getOrderProductDataStatistics(Request $params) {

        $requestParams = $params->all();
        $data = $this->orderStatisticsRepository->getOrderProductStatistics($requestParams);

        return $data;

    }



}
