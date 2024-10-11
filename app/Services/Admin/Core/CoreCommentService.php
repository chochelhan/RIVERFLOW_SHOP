<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeCommentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoreCommentService {

    protected $commentRepository;
    public function __construct(CustomizeCommentRepository $commentRepository) {

        $this->commentRepository = $commentRepository;

    }

    // 댓글저장
    public function insertComment(Request $params) {
        $requestParams = $params->all();
        $userInfo = Auth::user();

        $requestParams['user_id'] =$userInfo->id;
        $requestParams['name'] =$userInfo->name;
        if(!empty($requestParams['pid']))$requestParams['depth'] = 2;
        $fieldsets = makeFieldset($this->commentRepository->useFields,$requestParams);
        $id = $this->commentRepository->insertComment($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    public function getCommentList(Request $params) {
        return $this->commentRepository->getCommentList($params->input('parentId'),$params->input('parentType'));
    }

    /* 수정
    public function updateComment(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newComment')return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->commentRepository->useFields,$requestParams);
        $id = $this->commentRepository->updateComment($requestParams['id'],$fieldsets);
        if($id) {
            $data = $this->commentRepository->getCommentInfo($id);
            $status = 'success';
        } else {
            $data = '';
            $status = 'error';
        }

        return ['status'=>$status,'data'=>$data];
    }

    // 삭제
    public function deleteComment(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $id = $this->commentRepository->deleteComment($requestParams['id']);
        if($id) {
            $list =  $this->commentRepository->getCommentList();
            $grank = 1;
            foreach($list as $val) {
                $targetFieldsets = ['brank'=>$grank];
                $this->commentRepository->updateComment($val->id,$targetFieldsets);
                $grank++;
            }
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$id];
    }
    */

}
