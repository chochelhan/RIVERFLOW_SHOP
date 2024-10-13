<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiProductService;

/**
* 상품
*
**/
class CoreApiProductController extends CoreApiAuthHeaderController
{
    protected $productService;

    public function __construct(Request $request, CustomizeApiProductService $productService) {
         parent::__construct($request);
        $this->productService = $productService;

    }

    // 상품문의 저장
    public function insertProductInquire(Request $request) {

        $data = $this->productService->insertProductInquire($request);
        return $this->apiResponse($data,$this->newToken);

    }

}
