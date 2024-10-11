<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiShippingRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;


class CoreApiMyShippingService extends CoreApiAuthHeader {

    protected $shippingRepository;


    public function __construct(Request $request,CustomizeApiShippingRepository $shippingRepository) {

        parent::__construct($request);

        $this->shippingRepository = $shippingRepository;

    }

    public function getMyShippingList() {
        if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
        }

        $data = $this->shippingRepository->getShippingList($this->isLoginInfo->id);
        return ['status'=>'success','data'=>$data];
    }
    public function getMyShippingInfo(Request $request) {
        if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
        }

        $data = $this->shippingRepository->getMyShippingInfo($this->isLoginInfo->id,$request->input('id'));
        return ['status'=>'success','data'=>$data];
    }
    public function updateMyShipping(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $requestParams = $request->all();
        $shippingFieldset = makeFieldset($this->shippingRepository->useFields,$requestParams);
        $shippingFieldset['user_id'] = $this->isLoginInfo->id;
        if(empty($request->input('id'))) {
            $data = $this->shippingRepository->insertShipping($shippingFieldset);
            $status = ($data)?'success':'error';
        } else {
            $data = $this->shippingRepository->updateShipping($request->input('id'),$shippingFieldset);
            $status = ($data)?'success':'error';
        }
        return ['status'=>$status,'data'=>$data];
    }

    public function deleteMyShipping(Request $request) {
        if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
        }

        $data = $this->shippingRepository->deleteShipping($this->isLoginInfo->id,$request->input('id'));
        return ['status'=>'success','data'=>$data];
    }

}