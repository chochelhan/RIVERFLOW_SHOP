<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeProductInquireService;

/**
* 상품
*
**/
class CoreProductInquireController extends Controller
{
    protected $productInquireService;


    public function __construct(CustomizeProductInquireService $productInquireService) {
        $this->productInquireService = $productInquireService;

    }


    /*** 상품 문의 삭제 **/
    public function deleteInquire(Request $request) {
        $data = $this->productInquireService->deleteInquire($request);
       return restResponse($data);

    }

    /*** 상품 문의 답변 **/
    public function updateInquire(Request $request) {
        $data = $this->productInquireService->updateInquire($request);
       return restResponse($data);

    }
}
