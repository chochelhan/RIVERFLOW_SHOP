<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiBoardService;

/**
* 장바구니
**/
class CoreApiBoardViewController extends CoreApiAuthHeaderController
{
    protected $boardService;

    public function __construct(Request $request, CustomizeApiBoardService $boardService) {
        parent::__construct($request);
        $this->boardService = $boardService;
    }

    public function getArticleList(Request $request) {

        if(!$request->has('bid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->getArticleList($request);
        return $this->apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }
    public function getArticleListByBtype(Request $request) {

        if(!$request->has('btype')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->getArticleListByBtype($request);
        return $this->apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }

    public function getArticleInfo(Request $request) {

        if(!$request->has('bid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->restResponse($data);
        }
        $data = $this->boardService->getArticleInfo($request);
        return $this->apiResponse($data,$this->newToken);
    }
}
