<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use Illuminate\Http\Request;

class CoreProductBrandService {

    protected $productBrandRepository;

    public function __construct(CustomizeProductBrandRepository $productBrandRepository) {

        $this->productBrandRepository = $productBrandRepository;
    }

    // 저장
    public function insertBrand(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->productBrandRepository->useFields,$requestParams);
        $rank = $this->productBrandRepository->getMaxRank();
        $fieldsets['brank'] = $rank + 1;
        $id = $this->productBrandRepository->insertBrand($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 수정
    public function updateBrand(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newBrand')return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->productBrandRepository->useFields,$requestParams);
        $id = $this->productBrandRepository->updateBrand($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 삭제
    public function deleteBrand(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->productBrandRepository->deleteBrand($requestParams['id']);
        if($id) {
            $list =  $this->productBrandRepository->getBrandList();
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['brank'=>$grank];
                $this->productBrandRepository->updateBrand($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    // 순서 변경
    public function sequenceBrand(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->productBrandRepository->getBrandInfo($requestParams['id']);
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['brank'] = $row->brank;
        $rankInfo = $this->productBrandRepository->sequenceBrandInfo($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['brank'=>$rankInfo['rank']];
            $this->productBrandRepository->updateBrand($row->id,$fieldsets);

            $targetFieldsets = ['brank'=>$row->brank];
            $this->productBrandRepository->updateBrand($rankInfo['targetId'],$targetFieldsets);
            $data = $this->productBrandRepository->getBrandList();
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];

    }

    // 목록
    public function getBrandList() {
        return $this->productBrandRepository->getBrandList();
    }

}
