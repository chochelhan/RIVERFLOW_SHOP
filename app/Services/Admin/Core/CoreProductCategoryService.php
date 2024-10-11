<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use Illuminate\Http\Request;

class CoreProductCategoryService {

    protected $productCategoryRepository;

    public function __construct(CustomizeProductCategoryRepository $productCategoryRepository) {

        $this->productCategoryRepository = $productCategoryRepository;
    }

    // 저장
    public function insertCategory(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->productCategoryRepository->useFields,$requestParams);
        $rank = $this->productCategoryRepository->getMaxRankByDepth($params->input('depth'),$params->input('pcode'));
        $fieldsets['crank'] = $rank + 1;
        $id = $this->productCategoryRepository->insertCategory($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }

    // 수정
    public function updateCategory(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newCategory')return ['status'=>'fail','data'=>''];

        $fieldsets = makeFieldset($this->productCategoryRepository->useFields,$requestParams);
        $id = $this->productCategoryRepository->updateCategory($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 삭제
    public function deleteCategory(Request $params) {

        if(!$params->input('id'))return ['status'=>'fail','data'=>''];
        $result = $this->productCategoryRepository->deleteCategory($params->input('id'));
        $row = $result['row'];
        if($row) {
            $list = $this->productCategoryRepository->getCategoryListByDepth($row->depth,$row->pcode);
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['crank'=>$grank];
                $this->productCategoryRepository->updateCategory($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$params->input('id')];
    }
    // 순서 변경
    public function sequenceCategory(Request $params) {

        $requestParams = $params->all();
         if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->productCategoryRepository->getCategoryInfo($params->input('id'));
        if(!$row)return ['status'=>'fail','data'=>''];
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['crank'] = $row->crank;
        $rowData['depth'] = $row->depth;
        $rowData['pcode'] = $row->pcode;
        $rankInfo = $this->productCategoryRepository->sequenceCategoryInfo($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['crank'=>$rankInfo['rank']];
            $this->productCategoryRepository->updateCategory($row->id,$fieldsets);

            $targetFieldsets = ['crank'=>$row->crank];
            $this->productCategoryRepository->updateCategory($rankInfo['targetId'],$targetFieldsets);
            $data = 'success';
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];
    }

    // 목록
    public function getCategoryList() {
        return $this->productCategoryRepository->getCategoryList();
    }

}
