<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiOrderRepository;
use App\Repositories\Repository\Base\BaseSettingDeliveryRepository;
use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;


class CoreApiDeliveryTrackerService extends CoreApiAuthHeader {

    protected $orderRepository;
    protected $deliveryRepository;
    private $trackerUrl = 'https://apis.tracker.delivery/carriers/{{companyId}}/tracks/{{sendNumber}}';
  
    public function __construct(Request $request,
                                CustomizeApiOrderRepository $orderRepository,
                                BaseSettingDeliveryRepository $deliveryRepository) {


        parent::__construct($request);

        $this->orderRepository = $orderRepository;
        $this->deliveryRepository = $deliveryRepository;

    }

    // 배송조회
    public function getDeliveryTracker(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        if(empty($request->input('oid'))) {
            return ['status'=>'emptyField','data'=>''];
        }
        $oid = $request->input('oid');
        $orderList =  $this->orderRepository->getMyOrderDetail($this->isLoginInfo->id,$oid);
        $orderInfo = '';
        $ostatus = 'DI';
        foreach($orderList as $val) {
            if($val->deliveryCompany && $val->sendNumber && !$orderInfo) {
                $orderInfo = $val;
            }
            if($val->ordStatus!='DI')$ostatus = 'DC';
        }
        $data = [];
        if($orderInfo) {
            if($orderInfo->deliveryId) {
                $info = $this->deliveryRepository->getDeliveryInfo($orderInfo->deliveryId);
                if($info && $info->dmethod == 'direct') {
                    $data = 'direct';
                } else if($info && $info->dmethod != 'direct') {


                    $deliveryCompanyList = config('delivery');
                    $trackerFlag = false;
                    $companyName = '';
                    $companyTel = '';
                    foreach($deliveryCompanyList as $dcomp) {
                        if($dcomp['id'] == $orderInfo->deliveryCompany) {
                            if(!empty($dcomp['tracker']) && $dcomp['tracker']=='yes') {
                                $trackerFlag = true;
                            }
                            $companyName = $dcomp['name'];
                            $companyTel = $dcomp['tel'];
                        }


                   }
                   $data['companyName'] = $companyName;
                   $data['companyTel'] = $companyTel;
                   $data['ostatus'] = $ostatus;

                   if($trackerFlag) {
                        $data['trackerList'] = json_decode($orderInfo->deliverTracker);
                        $data['stype'] = 'tracker';
                   } else {
                        $data['trackerList']  = $this->getTracker($orderInfo->deliveryCompany,$orderInfo->sendNumber);
                        $data['stype'] = 'search';
                   }
                }
            }
        }
        if(!empty($data['trackerList'])) {
            return ['status'=>'success','data'=>$data];
        } else {
            return ['status'=>'message','data'=>''];
        }
    }
    private function getTracker($companyId,$sendNumber) {

        $this->trackerUrl = str_replace('{{companyId}}',$companyId,$this->trackerUrl);
        $this->trackerUrl = str_replace('{{sendNumber}}',$sendNumber,$this->trackerUrl);

        $ch = curl_init(); //curl 사용 전 초기화 필수(curl handle)
        curl_setopt($ch, CURLOPT_URL,$this->trackerUrl); //URL 지정하기
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec ($ch);
        curl_close($ch);

        return $data;

    }



}
