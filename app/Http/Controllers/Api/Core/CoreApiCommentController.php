<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiCommentService;

/**
*
**/
class CoreApiCommentController extends CoreApiAuthHeaderController
{
    protected $commentService;

    public function __construct(Request $request, CustomizeApiCommentService $commentService) {
        parent::__construct($request);
        $this->commentService = $commentService;
    }

    public function insertComment(Request $request) {

        if(!$request->has(['parentId','parentType','content'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->commentService->insertComment($request);
        return $this->apiResponse($data,$this->newToken);
    }

    public function updateComment(Request $request) {

         if(!$request->has(['parentId','parentType','id'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
         }
        $data = $this->commentService->updateComment($request);
        return $this->apiResponse($data,$this->newToken);
    }

    public function deleteComment(Request $request) {

        if(!$request->has('id')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->commentService->deleteComment($request);
        return $this->apiResponse($data,$this->newToken);
    }
    public function getCommentList(Request $request) {
        if(!$request->has(['parentId','parentType'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->commentService->getCommentList($request);
        return $this->apiResponse($data,$this->newToken);
    }



}
