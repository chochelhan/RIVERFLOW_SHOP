<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeOrderRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderClaimRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeCouponRepository;
use App\Repositories\Repository\Admin\Customize\CustomizePointRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeMemberRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingDeliveryRepository;

use App\Services\Admin\Core\RelationOstatus\RelationOstatusService;

use Illuminate\Http\Request;

class CoreOrderService  {

    protected $orderRepository;
    protected $claimRepository;
    protected $couponRepository;
    protected $pointRepository;
    protected $memberRepository;
    protected $siteRepository;
    protected $ostatusService;
    protected $deliveryRepository;

    public function __construct(CustomizeOrderRepository $orderRepository,
                                CustomizeOrderClaimRepository $claimRepository,
                                CustomizeCouponRepository $couponRepository,
                                CustomizePointRepository $pointRepository,
                                CustomizeSettingSiteRepository $siteRepository,
                                CustomizeMemberRepository $memberRepository,
                                CustomizeSettingDeliveryRepository $deliveryRepository,
                                RelationOstatusService $ostatusService) {

        $this->orderRepository = $orderRepository;
        $this->claimRepository = $claimRepository;
        $this->couponRepository = $couponRepository;
        $this->pointRepository = $pointRepository;
        $this->memberRepository = $memberRepository;
        $this->siteRepository = $siteRepository;
        $this->ostatusService = $ostatusService;
        $this->deliveryRepository = $deliveryRepository;

    }

    // 주문목록 불러오기 (전체)
    public function getOrderList(Request $params) {

        $requestParams = $params->all();

        $list['ostatusList'] =  $this->orderRepository->ostatusList;
        
        $list['ostatusCancleList'] = $this->orderRepository->cancleList;
        $list['ostatusReturnList'] = $this->orderRepository->returnList;
        $list['ostatusExchangeList'] = $this->orderRepository->exchangeList;
        $list['ostatusRefundList'] = $this->orderRepository->refundList;
        $list['paymethodList'] = $this->orderRepository->paymethodList;

        $list['orderList'] = $this->orderRepository->getOrderList($requestParams);

        $list['deliveryInfo'] = $this->deliveryRepository->getDeliveryUseList();

        $siteInfo = $this->siteRepository->getSiteInfo();
        if($siteInfo && $siteInfo->delivery) {
            $deliiveryCompanyInfo = json_decode($siteInfo->delivery);
            if(!empty($deliiveryCompanyInfo->duseCompany)) {
                $list['duseCompany'] = $deliiveryCompanyInfo->duseCompany;
            }
        }
        return $list;

    }
    // 주문목록 불러오기
    public function getOrderDataList(Request $params) {

        $requestParams = $params->all();
        $list = $this->orderRepository->getOrderList($requestParams);
        return $list;

    }

    // 주문상세
    public function getOrderDetail(Request $params) {

        $data = $this->getOrderProduct($params->input('id'));
        if($data && $data['orderInfo']->deliveryId) {
            $data['deliveryInfo'] = $this->deliveryRepository->getDeliveryInfo($data['orderInfo']->deliveryId);
        }

        $data['paymethodList']  = $this->orderRepository->paymethodList;
        $data['ostatusList'] = $this->orderRepository->ostatusList;
        $data['ostatusCancleList'] = $this->orderRepository->cancleList;
        $data['ostatusReturnList'] = $this->orderRepository->returnList;
        $data['ostatusExchangeList'] = $this->orderRepository->exchangeList;
        $data['ostatusRefundList'] = $this->orderRepository->refundList;
        $data['paymethodList'] = $this->orderRepository->paymethodList;
        $siteInfo = $this->siteRepository->getSiteInfo();
        if($siteInfo && $siteInfo->delivery) {
            $deliiveryCompanyInfo = json_decode($siteInfo->delivery);
            if(!empty($deliiveryCompanyInfo->duseCompany)) {
                $data['duseCompany'] = $deliiveryCompanyInfo->duseCompany;
            }
        }

        return $data;
    }

    // 주문상품 데이타만 가져오기
    private function getOrderProduct(int $oid) {

        $data['orderInfo'] = $this->orderRepository->getOrderInfo($oid);
        $data['productList'] = $this->orderRepository->getOrderProductList($oid);
        $ostatusData = [];
        foreach($data['productList'] as $info) {
            if(!empty($this->orderRepository->ostatusList[$info->ostatus])) {
               $ostatusData[$info->ostatus] = (!empty($ostatusData[$info->ostatus]))?$ostatusData[$info->ostatus]+1:1;

            } else if(!empty($this->orderRepository->cancleList[$info->ostatus])) { // 취소
                $ostatusData['CZ'] = (!empty($ostatusData['CZ']))?$ostatusData['CZ']+1:1;

            } else if(!empty($this->orderRepository->returnList[$info->ostatus])) { // 반품
                $ostatusData['RZ'] = (!empty($ostatusData['RZ']))?$ostatusData['RZ']+1:1;

            } else if(!empty($this->orderRepository->exchangeList[$info->ostatus])) { // 교환
                $ostatusData['EZ'] = (!empty($ostatusData['EZ']))?$ostatusData['EZ']+1:1;

            } else if(!empty($this->orderRepository->refundList[$info->ostatus])) { // 환불
                $ostatusData['FZ'] = (!empty($ostatusData['FZ']))?$ostatusData['FZ']+1:1;
            }
        }
        $data['ostatusData'] = $ostatusData;
        $data['orderHistory'] = $this->orderRepository->getOrderHistory($oid);
        return $data;
    }

    // 주문상태 변경
    public function updateOrderStatus(Request $request) {
        if(!$request->has(['ids','ostatus'])) {
            return ['status'=>'emptyField','data'=>''];

        }
        if(!empty($request->input('deliveryData'))) {
            $_deliveryData = json_decode($request->input('deliveryData'));
            foreach($_deliveryData as $id=>$info) {
                $deliveryData[$id] = $info;
            }
        } else {
            $deliveryData = [];
        }

        $ids = $request->input('ids');
        $orderList = $this->orderRepository->getOrderListByIds($ids);
        $orderDataList = [];
        foreach($orderList as $val) {
            $orderDataList[$val->id]['order'] = $val;
            $orderDataList[$val->id]['product'][] = $val;
        }
        $opIdsInfo = [];
        $opIds = [];
        $trackerDatas = [];
        foreach($orderDataList as $orderData) {

            $orderInfo = $orderData['order'];
            $orderProducts = $orderData['product'];
            switch($request->input('ostatus')) {
                case 'income':
                case 'notpay':
                    $orderResult = $this->orderRepository->updateOrderStatus($orderInfo->id,$request->input('ostatus'));
                break;
            }
            $reserverPointFlag = 'empty';
            $trackerCheck = false;
            $updateStatusOpIds = [];
            $updateStatusOpIdsInfo = [];
            foreach($orderProducts as $val) {
                $checkOstatus = substr($val->ostatus,0,1);
                switch($checkOstatus) {
                    case 'C':
                    case 'R':
                    case 'E':
                    case 'F':
                        $reserverPointFlag = 'no';
                        break;
                    default:
                        $opIds[] = $val->opId;
                        $updateStatusOpIds[] = $val->opId;
                        $opIdsInfo[$val->opId] = ['ostatus'=>$val->opOstatus,'oid'=>$orderInfo->id]; //
                        $updateStatusOpIdsInfo[$val->opId] = ['ostatus'=>$val->opOstatus,'oid'=>$orderInfo->id]; //

                        if($request->input('ostatus') == 'DI' && $val->opOstatus=='DR')$trackerCheck = true;

                        if($request->input('ostatus')=='OC' && $val->opOstatus=='DC') { // 배송완료에서 구매확정으로 온경우
                            if($reserverPointFlag == 'empty' && $orderInfo->reservePoint) {
                                $reserverPointFlag = 'plus';
                            }
                        } else if($request->input('ostatus') =='DC' && $val->opOstatus=='OC') { // 구매확정에서 배송완료로 온경우
                             if($reserverPointFlag == 'empty' && $orderInfo->reservePoint) {
                                $reserverPointFlag = 'minus';
                             }
                        }
                        break;
                }
            }
            if($reserverPointFlag == 'minus' || $reserverPointFlag == 'plus') { // 포인트 복구 또는 적립
                $pointParams = [];
                $pointParams['user_id'] = $orderInfo->user_code;
                $pointParams['point'] = $orderInfo->reservePoint; // 포인트
                $pointParams['oid'] = $orderInfo->id; // 주문고유키

                if($reserverPointFlag == 'plus') {
                    $pointParams['pointMsg'] = $this->pointRepository->PCODES['OC'];
                    $pointParams['ptype'] = 'plus';
                    $pointParams['pcode'] = 'OC';
                } else {
                    $pointParams['pointMsg'] = $this->pointRepository->PCODES['ADC'];
                    $pointParams['ptype'] = 'minus';
                    $pointParams['pcode'] = 'ADC';

                }
                $pointResult = $this->pointRepository->insertPoint($pointParams);
                if(!empty($pointResult))$this->memberRepository->updateMemberPoint($pointParams);


            }

            $updateParams = [];
            $eventParams = [];

            if($request->input('ostatus') == 'DI' && $trackerCheck ) {

                if(!empty($deliveryData[$orderInfo->id])) {

                    $deliveryParams['deliveryCompany'] = $deliveryData[$orderInfo->id]->deliveryCompany;
                    $deliveryParams['sendNumber'] = $deliveryData[$orderInfo->id]->sendNumber;
                            // 이벤트 정보 (주문히스토리)
                    $eventParams['content'] = ['deliveryCompany'=>$deliveryParams['deliveryCompany'],
                                                         'sendNumber'=>$deliveryParams['sendNumber']];

                    $this->orderRepository->updateOrder($orderInfo->id,$deliveryParams);

                    $deliveryCompanyList = config('delivery');
                    foreach($deliveryCompanyList as $dcomp) {
                        if($dcomp['id'] == $deliveryParams['deliveryCompany'] && !empty($dcomp['tracker']) && $dcomp['tracker']=='yes') {
                            $trackerDatas[] = $deliveryParams;
                        }

                    }



                }
            }
            $updateParams['ostatus'] = $request->input('ostatus');
            $this->orderRepository->updateOrderProductStatus($updateStatusOpIds,$updateParams);

            // 이벤트 정보 (주문히스토리)
            $eventParams['nowstatus'] = $request->input('ostatus');
            $eventParams['oldstatus'] = $updateStatusOpIdsInfo;
            \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));

        }


        if(count($trackerDatas)>0) {
            $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
            if(!empty($siteInfos)) {
                $siteEnvData = ($siteInfos->siteEnv)?json_decode($siteInfos->siteEnv):'';
                if($siteEnvData && !empty($siteEnvData->siteEnv)) {
                    if($siteEnvData->siteEnv == 'production') {
                        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
                        $fileName = $path.'/license/license.txt';
                        setDeliveryTracker($trackerDatas,$fileName);
                    }
                }
            }
        }
        if(count($opIds)>0) {


            /*** 메일 ,문자 , 재고변경 ****/
            $relServiceParams['nowstatus'] = $request->input('ostatus');
            $relServiceParams['oldstatus'] = $opIdsInfo;

            $this->ostatusService->relationOrderService($relServiceParams);

            if(!empty($request->input('type')) && $request->input('type') == 'single') {
                $data = $this->getOrderProduct($ids[0]);

                return ['status'=>'success','data'=>$data]; //
            } else return ['status'=>'success','data'=>''];

        } else {
            return ['status'=>'error','data'=>''];
        }


    }


    // 클레임 상태 변경
    public function updateClaimStatus(Request $request) {
        if(!$request->has(['ids','ostatus'])) {
            return ['status'=>'emptyField','data'=>''];

        }
        $ids = $request->input('ids');
        $opids = [];
        $opIdsInfo = [];
        $claimDatas = $this->claimRepository->getClaimDataListByIds($ids);
        foreach($claimDatas as $cdata) {
            $opIdsInfo[$cdata->opid] = ['ostatus'=>$cdata->claimType,'oid'=>$cdata->oid,'oldOstatus'=>$cdata->oldOstatus]; //
            $opids[] = $cdata->opid;
        }
        /// 주문상품 정보 업데이트
        switch($request->input('ostatus')) {
            case 'CD':// 취소 거부
            case 'RD':// 반품 거부
            case 'FD':// 환불 거부
            case 'ED':// 교환 거부
                ////// 기존 주문상태로 복구 시킨다
                foreach($opIdsInfo as $opid=>$opData) {
                    $oldOstatus = $opData['oldOstatus'];
                    $result = $this->orderRepository->updateOrderProductStatusBySingle($opid,$oldOstatus);
                }
                break;
            default:
                $updateParams['ostatus'] = $request->input('ostatus');
                $updateParams['claimDate'] = date('Y-m-d H:i:s'); // 클레임상태 변경일 저장
                $result = $this->orderRepository->updateOrderProductStatus($opids,$updateParams);
                break;
        }
        $claimResult =  '';
        // 클레임 정보 업데이트
        if($result) {
            $claimParams['claimType'] = $request->input('ostatus');
            $claimResult =  $this->claimRepository->updateClaimStatus($ids,$claimParams);
        }

        if($claimResult) {
            // 이벤트 정보 (주문히스토리)
            $eventParams['nowstatus'] = $request->input('ostatus');
            $eventParams['oldstatus'] = $opIdsInfo;
            \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));

            if($request->input('ostatus')=='EC') {
                /*** 메일 ,문자 , 재고변경 ****/
                $this->ostatusService->relationOrderService($eventParams);
            }
            return ['status'=>'success','data'=>''];

        } else return ['status'=>'fail','data'=>''];
    }


    // 취소목록 불러오기 (전체)
    public function getCancleList(Request $params) {

        $requestParams = $params->all();

        $list['orderList'] = $this->claimRepository->getClaimList($requestParams,'cancle');
        $list['ostatusCancleList'] = $this->orderRepository->cancleList;
        $list['paymethodList'] = $this->orderRepository->paymethodList;
        return $list;
    }
    // 반품목록 불러오기 (전체)
    public function getReturnList(Request $params) {

        $requestParams = $params->all();

        $list['orderList'] = $this->claimRepository->getClaimList($requestParams,'return');
        $list['ostatusCancleList'] = $this->orderRepository->returnList;
        $list['paymethodList'] = $this->orderRepository->paymethodList;

        return $list;
    }
    // 교환목록 불러오기 (전체)
    public function getExchangeList(Request $params) {

        $requestParams = $params->all();

        $list['orderList'] = $this->claimRepository->getClaimList($requestParams,'exchange');
        $list['ostatusCancleList'] = $this->orderRepository->exchangeList;
        $list['paymethodList'] = $this->orderRepository->paymethodList;

        return $list;
    }
    // 환불목록 불러오기 (전체)
    public function getRefundList(Request $params) {

        $requestParams = $params->all();

        $list['orderList'] = $this->claimRepository->getClaimList($requestParams,'refund');
        $list['ostatusCancleList'] = $this->orderRepository->refundList;
        $list['ostatusCancleList2'] = $this->orderRepository->cancleList;
        $list['ostatusCancleList3'] = $this->orderRepository->returnList;


        $list['paymethodList'] = $this->orderRepository->paymethodList;

        return $list;
    }


    // 클레임 데이타 불러오기
    public function getClaimDataList(Request $params,string $claimType) {

        $requestParams = $params->all();
        $list = $this->claimRepository->getClaimList($requestParams,$claimType);
        return $list;

    }

    // 환불처리
    public function activeRefund(Request $request) {
        if(!$request->has('id')) {
            return ['status'=>'emptyField','data'=>''];
        }
        $id = $request->input('id');
        $opids = [];
        $opIdsInfo = [];
        $ids = [];
        $claimInfo ='';
        $claimType = '';
        $claimDatas = $this->claimRepository->getClaimDataListById($id);
        foreach($claimDatas as $cdata) {
            if(!$claimType)$claimType = $cdata->claimType;
            if(!$claimInfo)$claimInfo = $cdata;

            $opIdsInfo[$cdata->opid] = ['ostatus'=>$cdata->claimType,
                                        'oid'=>$cdata->oid,
                                        'oldOstatus'=>$cdata->oldOstatus,
                                        'recoverPrice'=>$cdata->recoverPrice,// 환불 금액
                                        'recoverPoint'=>$cdata->recoverPoint,// 환불포인트
                                        'recoverCouponId'=>$cdata->recoverCouponId];
            $opids[] = $cdata->opid;
            $ids[]  = $cdata->id;
        }
        $changeOstatus = '';
        switch($claimType) {
            case 'RA':
                $changeOstatus = 'RC';
                break;
            case 'CA':
                $changeOstatus = 'CC';
                break;
        }
        if(!$changeOstatus || !$claimInfo) {
            return ['status'=>'error','data'=>''];
        }
        $orderInfo = $this->orderRepository->getOrderInfo($claimInfo->oid);
        /** 쿠폰 및 포인트 복구 ***/
        if($orderInfo->is_member=='yes') {
            $recvParams = [];
            $recvFlag = false;
            if($claimInfo->recoverPoint) {
                $recvParams['pcode'] =  $changeOstatus;
                $recvParams['usePoint'] = $claimInfo->recoverPoint;
                $recvFlag = true;

            }
            if($claimInfo->recoverCouponId) {
                $recvParams['useCouponId'] = $claimInfo->recoverCouponId;
                $recvFlag = true;
            }
            if($recvFlag) {
                $recvParams['oid'] = $claimInfo->oid;
                $this->discountRecoverAll($recvParams,$orderInfo->user_code);
            }
        }
        if($claimInfo->recoverPrice) { // pg 결제시에 환불 완료 처리한다

        }

        $updateParams['ostatus'] = $changeOstatus;
        $updateParams['claimDate'] = date('Y-m-d H:i:s'); // 클레임상태 변경일 저장
        $result = $this->orderRepository->updateOrderProductStatus($opids,$updateParams);
        $claimResult =  '';
        // 클레임 정보 업데이트
        if($result) {
            $claimParams['claimType'] = $changeOstatus;
            $claimResult =  $this->claimRepository->updateClaimStatus($ids,$claimParams);
        }

        if($claimResult) {

            // 이벤트 정보 (주문히스토리)
            $eventParams['nowstatus'] = $changeOstatus;
            $eventParams['oldstatus'] = $opIdsInfo;
            \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));


            /*** 메일 ,문자 , 재고변경 ****/
            $this->ostatusService->relationOrderService($eventParams);

            return ['status'=>'success','data'=>''];

        } else return ['status'=>'fail','data'=>''];
    }

    private function discountRecoverAll(array $params,int $user_id) {

        if(!empty($params['useCouponId'])) {

            $this->couponRepository->updateCouponPublish($params['useCouponId'],['cuse'=>'no']); // 쿠폰 복구

        }

        if(!empty($params['usePoint'])) {
           $ptParams['user_id'] = $user_id;
           $ptParams['ptype'] = 'plus';
           $ptParams['point'] = $params['usePoint'];
           $ptParams['oid'] = $params['oid'];
           $ptParams['pointMsg'] = $this->pointRepository->PCODES[$params['pcode']];
           $ptParams['pcode'] = $params['pcode'];

           $this->pointRepository->insertPoint($ptParams);
           $this->memberRepository->updateMemberPoint($ptParams);
        }

    }
}
