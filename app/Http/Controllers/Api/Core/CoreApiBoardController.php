<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiBoardService;

/**
*
**/
class CoreApiBoardController extends CoreApiAuthHeaderController
{
    protected $boardService;

    public function __construct(Request $request, CustomizeApiBoardService $boardService) {
        parent::__construct($request);
        $this->boardService = $boardService;
    }

    public function insertArticle(Request $request) {

        if(!$request->has('bid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->insertArticle($request);
        return $this->apiResponse($data,$this->newToken);
    }

    public function updateArticle(Request $request) {

        if(!$request->has(['bid','id'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->updateArticle($request);
        return $this->apiResponse($data,$this->newToken);
    }

    public function deleteArticle(Request $request) {

        if(!$request->has('id')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->deleteArticle($request);
        return $this->apiResponse($data,$this->newToken);
    }
    public function checkArticleUserPass(Request $request) {

        if(!$request->has(['id','user_pass'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->checkArticleUserPass($request);
        return $this->apiResponse($data,$this->newToken);
    }

    public function insertArticleTempImage(Request $request) {

        $data = $this->boardService->insertArticleTempImage($request);
        return $this->restResponse($data);
    }

}
