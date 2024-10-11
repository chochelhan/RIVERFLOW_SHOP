<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiCartService;

/**
* 장바구니
*
**/
class CoreApiCartController extends CoreApiAuthHeaderController
{
    protected $cartService;

    public function __construct(Request $request, CustomizeApiCartService $cartService) {
        parent::__construct($request);
        $this->cartService = $cartService;
    }

    public function insertTempCart(Request $request) {

        $data = $this->cartService->insertTempCart($request);
        return apiResponse($data,$this->newToken);
    }

    public function insertCart(Request $request) {

        $data = $this->cartService->insertCart($request);
        return apiResponse($data,$this->newToken);
    }

    // 장바구니 구매수량 변경
    public function updateCartCamt(Request $request) {
        if(!$request->has(['id','camt'])) {
              $data = ['status'=>'emptyField','data'=>''];
              return apiResponse($data,$this->newToken);
        }

        $data = $this->cartService->updateCartCamt($request);
        return apiResponse($data,$this->newToken);
    }

    // 장바구니 삭제
    public function deleteCart(Request $request) {
        if(!$request->has('type')) {
              $data = ['status'=>'emptyField','data'=>''];
              return apiResponse($data,$this->newToken);
        }
        if($request->input('type')=='part') {
            if(!$request->has('id')) {
                $data = ['status'=>'emptyField','data'=>''];
                return apiResponse($data,$this->newToken);
            }
        }

        $data = $this->cartService->deleteCart($request);
        return apiResponse($data,$this->newToken);
    }


}
