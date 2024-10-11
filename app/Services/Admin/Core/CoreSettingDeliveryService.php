<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingDeliveryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingDeliveryLocalRepository;
use Illuminate\Http\Request;

class CoreSettingDeliveryService {

    protected $deliveryRepository;
    protected $deliveryLocalRepository;
    protected $siteRepository;


    public function __construct(CustomizeSettingSiteRepository $siteRepository,
                                CustomizeSettingDeliveryRepository $deliveryRepository,
                                CustomizeSettingDeliveryLocalRepository $deliveryLocalRepository) {

        $this->siteRepository = $siteRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryLocalRepository = $deliveryLocalRepository;

    }

    // 배송템플릿저장
    public function insertDelivery(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->deliveryRepository->useFields,$requestParams);
        $rank = $this->deliveryRepository->getMaxRank();
        $fieldsets['drank'] = $rank + 1;
        $id = $this->deliveryRepository->insertDelivery($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 배송템플릿수정
    public function updateDelivery(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newDelivery')return ['status'=>'fail','data'=>''];

        $fieldsets = makeFieldset($this->deliveryRepository->useFields,$requestParams);

        $id = $this->deliveryRepository->updateDelivery($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }

    // 배송템플릿 삭제
    public function deleteDelivery(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->deliveryRepository->deleteDelivery($requestParams['id']);

        if($id) {
             $list = $this->deliveryRepository->getDeliveryList();
             $grank = 1;
             foreach($list as $val) {
                $targetFieldsets = ['drank'=>$grank];
                $this->deliveryRepository->updateDelivery($val->id,$targetFieldsets);
                $grank++;
             }
             $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    // 배송템플릿 순서 변경
    public function sequenceDelivery(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->deliveryRepository->getDeliveryInfo($requestParams['id']);
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['drank'] = $row->drank;
        $rankInfo = $this->deliveryRepository->getSequenceDeliveryRank($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['drank'=>$rankInfo['rank']];
            $this->deliveryRepository->updateDelivery($params['id'],$fieldsets);

            $targetFieldsets = ['drank'=>$row->drank];
            $this->deliveryRepository->updateDelivery($rankInfo['targetId'],$targetFieldsets);
            $data = $this->deliveryRepository->getDeliveryList();
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];
    }

    // 템플릿 목록
    public function getDeliveryList() {
        $siteInfo = $this->siteRepository->getSiteInfo();
        if($siteInfo && $siteInfo->delivery)$data['groupType'] = json_decode($siteInfo->delivery);
        else $data['groupType'] = '';
        $data['templateList'] = $this->deliveryRepository->getDeliveryList();
        $data['localList'] = $this->deliveryLocalRepository->getDeliveryLocalList();
        return $data;
    }

    // 배송업체 정보
    public function getDeliveryCompanyInfo(Request $request) {

         $data['deliveryCompany'] = config('delivery');
         $siteInfo = $this->siteRepository->getSiteInfo();
         if($siteInfo && $siteInfo->delivery) {
            $deliiveryCompanyInfo = json_decode($siteInfo->delivery);
           // if(!empty($deliiveryCompanyInfo->ocday)) {
             //   $data['ocday'] = $deliiveryCompanyInfo->ocday;
            //}
            if(!empty($deliiveryCompanyInfo->duseCompany)) {
                $data['duseCompany'] = $deliiveryCompanyInfo->duseCompany;
            }
         }

         return ['status'=>'success','data'=>$data];
    }



}
