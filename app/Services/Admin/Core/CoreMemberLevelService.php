<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeMemberLevelRepository;
use Illuminate\Http\Request;

class CoreMemberLevelService {

    protected $memberLevelRepository;

    public function __construct(CustomizeMemberLevelRepository $memberLevelRepository) {

        $this->memberLevelRepository = $memberLevelRepository;
    }

    // 등급저장
    public function insertLevel(Request $params) {
        $requestParams = $params->all();
        $fieldsets = makeFieldset($this->memberLevelRepository->useFields,$requestParams);
        $rank = $this->memberLevelRepository->getMaxRank();
        $fieldsets['grank'] = $rank + 1;
        $id = $this->memberLevelRepository->insertLevel($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 등급수정
    public function updateLevel(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newLevel')return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->memberLevelRepository->useFields,$requestParams);
        $id = $this->memberLevelRepository->updateLevel($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 등급삭제
    public function deleteLevel(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->memberLevelRepository->deleteLevel($requestParams['id']);
        if($id) {
            $list =  $this->memberLevelRepository->getLevelList();
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['grank'=>$grank];
                $this->memberLevelRepository->updateLevel($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    // 등급 순서 변경
    public function sequenceLevel(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'] || !$requestParams['cmd'])return ['status'=>'fail','data'=>''];

        $row =$this->memberLevelRepository->getLevelInfo($requestParams['id']);
        $rowData['cmd'] = $requestParams['cmd'];
        $rowData['grank'] = $row->grank;
        $rankInfo = $this->memberLevelRepository->sequenceLevelInfo($rowData);
        if($rankInfo['rank']) {
            $fieldsets = ['grank'=>$rankInfo['rank']];
            $this->memberLevelRepository->updateLevel($row->id,$fieldsets);

            $targetFieldsets = ['grank'=>$row->grank];
            $this->memberLevelRepository->updateLevel($rankInfo['targetId'],$targetFieldsets);
            $data = $this->memberLevelRepository->getLevelList();
        } else {
            $data = 'stay';
        }
        return ['status'=>'success','data'=>$data];

    }

    // 등급목록
    public function getLevelList() {
        return $this->memberLevelRepository->getLevelList();
    }
}
