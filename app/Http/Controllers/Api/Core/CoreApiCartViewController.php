<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiCartService;

/**
* 장바구니
**/
class CoreApiCartViewController extends CoreApiAuthHeaderController
{
    protected $cartService;

    public function __construct(Request $request, CustomizeApiCartService $cartService) {
        parent::__construct($request);
        $this->cartService = $cartService;
    }

    public function getCartList(Request $request) {

        $data = $this->cartService->getCartList($request);
        return $this->apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }

}
