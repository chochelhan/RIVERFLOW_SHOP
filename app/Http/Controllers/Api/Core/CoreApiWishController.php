<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiWishService;

/**
* 상품
*
**/
class CoreApiWishController extends CoreApiAuthHeaderController
{
    protected $wishService;

    public function __construct(Request $request, CustomizeApiWishService $wishService) {
         parent::__construct($request);
        $this->wishService = $wishService;

    }
    // 관심 목록
    public function getMyWishList() {

        $data = $this->wishService->getMyWishList();
        return apiResponse($data,$this->newToken);

    }


    // 관심상품 저장/삭제
    public function updateProductWish(Request $request) {
        if(!$request->has(['pid'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return apiResponse($data,$this->newToken);
        }
        $params['pid'] = $request->input('pid');
        $params['type'] = 'product';
        $data = $this->wishService->updateWish($params);
        return apiResponse($data,$this->newToken);

    }

}
