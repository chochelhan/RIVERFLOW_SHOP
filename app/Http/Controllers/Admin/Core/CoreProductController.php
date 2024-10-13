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
class CoreProductController extends Controller
{
	protected $productService;
	protected $productCategoryService;
	protected $productBrandService;
	protected $productAddInfoService;

	public function __construct(CustomizeProductService         $productService,
	                            CustomizeProductCategoryService $productCategoryService,
	                            CustomizeProductBrandService    $productBrandService,
	                            CustomizeProductAddInfoService  $productAddInfoService)
	{


		$this->productService = $productService;
		$this->productCategoryService = $productCategoryService;
		$this->productBrandService = $productBrandService;
		$this->productAddInfoService = $productAddInfoService;

	}

	/**
	 *@ 상품 카테고리
	 **/
	/*** 상품 카테고리 저장 ***/
	public function insertCategory(Request $request)
	{
		$data = $this->productCategoryService->insertCategory($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/*** 상품 카테고리 수정 ***/
	public function updateCategory(Request $request)
	{
		$data = $this->productCategoryService->updateCategory($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 카테고리 삭제 **/
	public function deleteCategory(Request $request)
	{
		$data = $this->productCategoryService->deleteCategory($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 카테고리 순서 변경 **/
	public function sequenceCategory(Request $request)
	{
		$data = $this->productCategoryService->sequenceCategory($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/**
	 *@ 상품 브랜드
	 **/
	/*** 상품 브랜드 저장 **/
	public function insertBrand(Request $request)
	{
		$data = $this->productBrandService->insertBrand($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/*** 상품 브랜드 수정 **/
	public function updateBrand(Request $request)
	{
		$data = $this->productBrandService->updateBrand($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 브랜드 삭제 **/
	public function deleteBrand(Request $request)
	{
		$data = $this->productBrandService->deleteBrand($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 브랜드 순서 변경 **/
	public function sequenceBrand(Request $request)
	{
		$data = $this->productBrandService->sequenceBrand($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/**
	 *@ 상품 추가정보
	 **/
	/*** 상품 추가정보 저장 **/
	public function insertAddInfo(Request $request)
	{
		$data = $this->productAddInfoService->insertAddInfo($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/*** 상품 추가정보수정 **/
	public function updateAddInfo(Request $request)
	{
		$data = $this->productAddInfoService->updateAddInfo($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 추가정보 삭제 **/
	public function deleteAddInfo(Request $request)
	{
		$data = $this->productAddInfoService->deleteAddInfo($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/*** 상품 추가정보 순서 변경 **/
	public function sequenceAddInfo(Request $request)
	{
		$data = $this->productAddInfoService->sequenceAddInfo($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);

	}

	/**
	 *@ 상품
	 **/
	/*** 상품 저장 **/
	public function insertProduct(Request $request)
	{
		$data = $this->productService->insertProduct($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/*** 상품 수정 **/
	public function updateProduct(Request $request)
	{
		$data = $this->productService->updateProduct($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/*** 상품 상세 이미지 임시 저장 **********/
	public function insertProductTempImage(Request $request)
	{

		$data = $this->productService->insertTempImage($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	public function deleteProductInfoNotice(Request $request)
	{

		$data = $this->productService->deleteProductInfoNotice($request);
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

}
