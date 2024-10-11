<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiOrderRepository;
use Illuminate\Http\Request;

class TrackerService {

    protected $orderRepository;

    public function __construct(CustomizeApiOrderRepository $orderRepository) {

        $this->orderRepository = $orderRepository;
    }

    public function recordTracker(Request $request) {
        $path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        $fileName = $path.'/license/license.txt';
        $fp = fopen($fileName, "r") or die("파일을 열 수 없습니다！");
        $license = fgets($fp);
        fclose($fp);

        if(empty($request->bearerToken())) {
            return ['status'=>'error','data'=>'emptyToken'];
        }
        if($request->bearerToken() != $license) {
            return ['status'=>'error','data'=>'authFail'];
        }
        if(!$request->has('datas')) {
            return ['status'=>'error','data'=>'emptyField'];
        }
        $params = json_decode($request->input('datas'));
        $result = '';
        $status = 'message';
        $resultData = [];
        foreach($params->company as $key=>$company) {
            $invcNo = $params->invcNo[$key];
            $contents = $params->contents[$key];
            $ostatus = '';
            if(!empty($params->complete[$key]) && $params->complete[$key]=='yes') {
                $ostatus = 'DC';
            }
            $deliverTracker = '';
            if(!empty($params->contents[$key])) {
                $deliverTracker = $params->contents[$key];
            }
            $status = 'message';
            $infoList = $this->orderRepository->getOrderByDelivery($company,$invcNo);
            foreach($infoList as $info) {
                $updateParams = [];
                $updateProductParams = [];
                if($ostatus) {
                    $result = $this->orderRepository->updateOrderProductOstatusByOid($info->id,$ostatus);
                    $status = 'success';
                }

                if($deliverTracker) {
                   // $updateParams['updated_at'] = date('Y-m-d H:i:s');
                    $updateParams['deliverTracker'] = $deliverTracker;
                    $this->orderRepository->updateOrder($info->id,$updateParams);

                }
            }
            if($status=='success') {
                $keyNumber = $company.'_'.$invcNo;
                $resultData[$keyNumber] = $keyNumber;
            }


        }

        return ['status'=>'success','data'=>$resultData];
    }
}
