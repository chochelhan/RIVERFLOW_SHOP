<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeProductService;
use App\Services\Admin\Customize\CustomizeProductCategoryService;
use App\Services\Admin\Customize\CustomizeProductBrandService;
use App\Services\Admin\Customize\CustomizeProductAddInfoService;
use Illuminate\Http\response;
/**
* 상품
*
**/
class CoreProductViewController extends Controller
{
    protected $productService;
    protected $productCategoryService;
    protected $productBrandService;
    protected $productAddInfoService;


    public function __construct(CustomizeProductService $productService,
                                CustomizeProductCategoryService $productCategoryService,
                                CustomizeProductBrandService $productBrandService,
                                CustomizeProductAddInfoService $productAddInfoService) {
        $this->productService = $productService;
        $this->productCategoryService = $productCategoryService;
        $this->productBrandService = $productBrandService;
        $this->productAddInfoService = $productAddInfoService;
    }

    /**
    * 카테고리 전체 목록
    **/
    public function getCategoryList() {
        $list = $this->productCategoryService ->getCategoryList();
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 브랜드 목록
    **/
    public function getBrandList() {
        $list = $this->productBrandService ->getBrandList();
        return response()->json(['status' => 'success','data'=>$list]);
    }


    /**
    * 추가정보 목록
    **/
    public function getAddInfoList() {
        $list = $this->productAddInfoService ->getAddInfoList();
        return response()->json(['status' => 'success','data'=>$list]);
    }
    /**
    * 상품 목록
    **/
    public function getProductList(Request $request) {
        $list = $this->productService ->getProductList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 상품 데이타 목록
    **/
    public function getProductDataList(Request $request) {
        $list = $this->productService ->getProductDataList($request);
        return response()->json(['status' => 'success','data'=>$list]);
    }

    /**
    * 상품 등록/수정시 기본정보 및 상품정보 가져오기
    **/
    public function getProductRegistInfo(Request $request) {
        $info = $this->productService ->getRegistInfo($request);
	    return response()->json(['status' => 'success','data'=>$info]);
    }

    /**
    * 상품 정보 제공 고시 불러오기
    **/
    public function getProductInfoNoticeList(Request $request) {
        $data = $this->productService ->getProductInfoNoticeList($request);
	    return response()->json(['status' => 'success','data'=>$data]);
    }
}
