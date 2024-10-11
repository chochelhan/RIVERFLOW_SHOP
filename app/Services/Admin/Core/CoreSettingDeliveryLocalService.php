<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeSettingDeliveryLocalRepository;
use Illuminate\Http\Request;

class CoreSettingDeliveryLocalService {

    protected $deliveryLocalRepository;

    public function __construct(CustomizeSettingDeliveryLocalRepository $deliveryLocalRepository) {

        $this->deliveryLocalRepository = $deliveryLocalRepository;
    }

    // 지역별 추가 배송비저장
    public function insertDeliveryLocal(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->deliveryLocalRepository->useFields,$requestParams);
        $fieldsets['localData'] = json_encode($requestParams['localData']);
        $id = $this->deliveryLocalRepository->insertDeliveryLocal($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 지역별 추가 배송비수정
    public function updateDeliveryLocal(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newLocal')return ['status'=>'fail','data'=>''];

        $fieldsets = makeFieldset($this->deliveryLocalRepository->useFields,$requestParams);
        $fieldsets['localData'] = json_encode($requestParams['localData']);

        $id = $this->deliveryLocalRepository->updateDeliveryLocal($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }

    // 지역별 추가 배송비 삭제
    public function deleteDeliveryLocal(Request $params) {
        $id = $params->input('id');
        if(!$id)return ['status'=>'fail','data'=>''];
        $resultId = $this->deliveryLocalRepository->deleteDeliveryLocal($id);
        if($resultId) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];

    }


    // 지역별 추가 배송비 목록
    public function getDeliveryLocalList() {
        return $this->deliveryLocalRepository->getDeliveryLocalList();
    }

    // 지역별 추가 배송비 정보
    public function getDeliveryLocalInfo(request $request) {
        $info = $this->deliveryLocalRepository->getDeliveryLocalInfo($request->input('id'));
        if(!$info->id) {
            $data['status'] = 'fail';
            $data['data'] = '';
        } else {
            $data['status'] = 'success';
            $data['data'] = $info;

        }
        return $data;
    }

}
