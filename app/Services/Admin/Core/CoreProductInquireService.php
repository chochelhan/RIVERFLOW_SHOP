<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductInquireRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;

use Illuminate\Http\Request;

class CoreProductInquireService {

    protected $productInquireRepository;
    protected $productRepository;
    protected $productCategoryRepository;
    protected $productBrandRepository;

    public function __construct(CustomizeProductInquireRepository $productInquireRepository,
                                CustomizeProductCategoryRepository $productCategoryRepository,
                                CustomizeProductBrandRepository $productBrandRepository,
                                CustomizeProductRepository $productRepository) {

        $this->productInquireRepository = $productInquireRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->productRepository = $productRepository;
    }

    // 상품문의 목록( 모든 데이타를 가져옴)
    public function getInquireList(Request $params) {

        $requestParams = $params->all();
        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();

        $limit = ($params->input('limit'))?$params->input('limit'):20;
        $data['inquireList'] = $this->productInquireRepository->getProductInquireDataList($requestParams,$limit);

         return $data;
    }
    // 상품문의 데이타 목록
    public function getInquireDataList(Request $params) {
        $requestParams = $params->all();
        $limit = ($params->input('limit'))?$params->input('limit'):20;
        $data = $this->productInquireRepository->getProductInquireDataList($requestParams,$limit);
        return $data;
    }

    // 상품 문의 상세
    public function getInquireInfo(Request $params) {
        $data['info'] = $this->productInquireRepository->getProductInquireInfo($params->input('id'));
        $data['productInfo'] = $this->productRepository->getProductInfo($data['info']->pid);

        return $data;
    }

    // 상품 문의 답변
    public function updateInquire(Request $params) {

        $fieldset = ['content'=>$params->input('content'),'status'=>'complete'];
        $result = $this->productInquireRepository->updateProductInquire($params->input('id'),$fieldset);
        if($result) {
            $status = 'success';
        } else {
            $status = 'fail';
        }
        return ['status'=>$status,'data'=>''];
    }

    // 상품 문의 삭제
    public function deleteInquire(Request $params) {

        $result = $this->productInquireRepository->deleteProductInquire($params->input('ids'));
        if($result) {
            $status = 'success';
        } else {
            $status = 'fail';
        }
        return ['status'=>$status,'data'=>''];
    }

}
