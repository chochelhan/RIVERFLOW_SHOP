<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiProductService;
use App\Services\Api\Customize\CustomizeApiReviewService;

/**
* 상품
*
**/
class CoreApiProductViewController extends CoreApiAuthHeaderController
{
    protected $productService;
    protected $reviewService;

    public function __construct(Request $request,
                                CustomizeApiProductService $productService,
                                CustomizeApiReviewService $reviewService) {
        parent::__construct($request);

        $this->productService = $productService;
        $this->reviewService = $reviewService;

    }

    public function getProductList(request $request) {

        $data = $this->productService->getProductList($request);
        return $this->apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }

    public function getProductDataList(request $request) {

        $data = $this->productService->getProductDataList($request);
        return $this->apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }

    // 상품 상세
    public function getProductInfo(request $request) {

        $data = $this->productService->getProductInfo($request);
        return $this->apiResponse($data,$this->newToken);
    }

    // 상품과 연관된 다른상품
    public function getProductRelationList(request $request) {
        if(!$request->has('pid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->apiResponse($data,$this->newToken);
        }
        $data = $this->productService->getProductRelationList($request);
        return $this->apiResponse($data,$this->newToken);
    }


    // 상품문의 목록
    public function getProductInquireList(Request $request) {
        if(!$request->has('pid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->apiResponse($data,$this->newToken);
        }

        $data = $this->productService->getProductInquireList($request);
        return $this->apiResponse($data,$this->newToken);

    }

    // 상품리뷰 목록
    public function getProductReviewList(Request $request) {
        if(!$request->has('pid')) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->apiResponse($data,$this->newToken);
        }

        $data = $this->reviewService->getProductReviewList($request);
        return $this->apiResponse($data,$this->newToken);

    }

}
