<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductAddInfoRepository;
use Illuminate\Http\Request;

class CoreProductAddInfoService {

    protected $productAddInfoRepository;

    public function __construct(CustomizeProductAddInfoRepository $productAddInfoRepository) {

        $this->productAddInfoRepository = $productAddInfoRepository;
    }

    // 저장
    public function insertAddInfo(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->productAddInfoRepository->useFields,$requestParams);
        $rank = $this->productAddInfoRepository->getMaxRank();
        $fieldsets['brank'] = $rank + 1;
        $fieldsets['itemList'] = $requestParams['itemList'];

        $id = $this->productAddInfoRepository->insertAddInfo($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 수정
    public function updateAddInfo(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newAddInfo')return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->productAddInfoRepository->useFields,$requestParams);
        $fieldsets['itemList'] = $requestParams['itemList'];

        $id = $this->productAddInfoRepository->updateAddInfo($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 삭제
    public function deleteAddInfo(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->productAddInfoRepository->deleteAddInfo($requestParams['id']);
        if($id) {
            $list =  $this->productAddInfoRepository->getAddInfoList();
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['brank'=>$grank];
                $this->productAddInfoRepository->updateAddInfo($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    // 순서 변경
    public function sequenceAddInfo(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->productAddInfoRepository->getAddInfoInfo($requestParams['id']);
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['brank'] = $row->brank;
        $rankInfo = $this->productAddInfoRepository->sequenceAddInfo($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['brank'=>$rankInfo['rank']];
            $this->productAddInfoRepository->updateAddInfo($row->id,$fieldsets);

            $targetFieldsets = ['brank'=>$row->brank];
            $this->productAddInfoRepository->updateAddInfo($rankInfo['targetId'],$targetFieldsets);
            $data = $this->productAddInfoRepository->getAddInfoList();
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];

    }

    // 목록
    public function getAddInfoList() {
        return $this->productAddInfoRepository->getAddInfoList();
    }

}
