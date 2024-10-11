<?php
namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiCommentRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiBoardRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiCommentService extends CoreApiAuthHeader {


    protected $commentRepository;
    protected $boardRepository;

    public function __construct(Request $request,
                                CustomizeApiCommentRepository $commentRepository,
                                CustomizeApiBoardRepository $boardRepository) {

        parent::__construct($request);

        $this->commentRepository = $commentRepository;
        $this->boardRepository = $boardRepository;
    }

    // 댓글저장
    public function insertComment(Request $params) {
        $requestParams = $params->all();
        if(!empty($this->isLoginInfo)) {
            $requestParams['user_id'] =$this->isLoginInfo->id;
            $requestParams['name'] = ($this->isLoginInfo->nick)?$this->isLoginInfo->nick:$this->isLoginInfo->name;
        }


        if(!empty($requestParams['pid']))$requestParams['depth'] = 2;
        else $requestParams['depth'] = 1;

        $fieldsets = makeFieldset($this->commentRepository->useFields,$requestParams);
        $data = $this->commentRepository->insertComment($fieldsets);

        if($requestParams['depth']==1) {
            switch($requestParams['parentType']) {
                case 'board':
                    $articleRow = $this->boardRepository->getArticleInfo($requestParams['parentId']);
                    if($articleRow) {
                        $updateParams['commentCnt'] = $articleRow->commentCnt + 1;
                        $this->boardRepository->updateArticle($requestParams['parentId'],$updateParams);
                    }
                break;
            }
        }

        if($data) {
            $status = 'success';
        } else $status = 'error';
        return ['status'=>$status,'data'=>$data];
    }


    public function getCommentList(Request $request) {
        $params = $request->all();
        $data['commentList'] = $this->commentRepository->getCommentList($params);

        if(!empty($this->isLoginInfo)) {
            $data['memberInfo'] = $this->isLoginInfo;
        }

        return ['status'=>'success','data'=>$data];

    }

    // 수정
    public function updateComment(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        $fieldsets = makeFieldset($this->commentRepository->useFields,$requestParams);
        $id = $this->commentRepository->updateComment($requestParams['id'],$fieldsets);
        if($id) {
            $status = 'success';
        } else {

            $status = 'error';
        }
         $data = '';

        return ['status'=>$status,'data'=>$data];
    }

    // 삭제
    public function deleteComment(Request $request) {


        $row = $this->commentRepository->getCommentInfo($request->input('id'));
        if($row) {
            if($row->depth==1) {
                switch($row->parentType) {
                    case 'board':
                        $articleRow = $this->boardRepository->getArticleInfo($row->parentId);
                        if($articleRow) {
                            $updateParams['commentCnt'] = $articleRow->commentCnt - 1;
                            if($updateParams['commentCnt']<0)$updateParams['commentCnt'] = 0;

                            $this->boardRepository->updateArticle($row->parentId,$updateParams);
                        }
                    break;
                }
            }
            $this->commentRepository->deleteComment($request->input('id'));
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>''];
    }

}
