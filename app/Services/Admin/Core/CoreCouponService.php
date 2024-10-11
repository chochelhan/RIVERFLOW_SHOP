<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeCouponRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeMemberLevelRepository;

use Illuminate\Http\Request;

class CoreCouponService {

    protected $couponRepository;
    protected $productRepository;
    protected $productCategoryRepository;
    protected $productBrandRepository;
    protected $memberLevelRepository;

    public function __construct(CustomizeCouponRepository $couponRepository,
                                CustomizeProductRepository $productRepository,
                                CustomizeProductCategoryRepository $productCategoryRepository,
                                CustomizeProductBrandRepository $productBrandRepository,
                                CustomizeMemberLevelRepository $memberLevelRepository) {

        $this->couponRepository = $couponRepository;
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->memberLevelRepository = $memberLevelRepository;

    }

    /**
    *@ 쿠폰등록정보
    **/
    public function getCouponRegistInfo(Request $params) {
       $id = $params->input('id');
       $data = [];
       $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
       $data['brandList'] = $this->productBrandRepository->getBrandUseList();
       $data['levelList'] = $this->memberLevelRepository->getLevelList();
       if($id) {
            $couponInfo = $this->couponRepository->getCouponInfo($id);

            if($couponInfo->ptType=='single') {
                $productIds = json_decode($couponInfo->ptInData);
            } else {
                if($couponInfo->ptDeny == 'yes') {
                    $productIds = json_decode($couponInfo->ptOutData);
                }
            }
            if(!empty($productIds)) {
                $couponInfo->productList = $this->productRepository->getProductListByIds($productIds);
            }
            $data['couponInfo'] = $couponInfo;
       }

       return $data;
    }

    /**
    *@ 쿠폰 목록
    **/
    public function getCouponList(Request $params) {
       $requestParams = $params->all();
       $data = $this->couponRepository->getCouponList($requestParams,$params->input('limit'));
       return $data;
    }

    /**
    *@ 쿠폰 저장
    **/
    public function insertCoupon(Request $params) {

        $requestParams = $params->all();

        $fieldsets = makeFieldset($this->couponRepository->useFields,$requestParams);

        $ptOutData = null;
        $ptInData = null;
        switch($requestParams['ptType']) {
            case 'single':
                $ptInData = $requestParams['selectedProductList'];
                break;
            case 'category':
                $ptInData = $requestParams['selectedCategoryList'];
                $ptOutData = $requestParams['selectedProductList'];
                break;
            case 'brand':
                $ptInData = $requestParams['selectedBrandList'];
                $ptOutData = $requestParams['selectedProductList'];
                break;
            case 'all':
               $ptOutData = $requestParams['selectedProductList'];
               break;
        }

        $fieldsets['ptInData'] = $ptInData;
        $fieldsets['ptOutData'] = $ptOutData;
        $id = $this->couponRepository->insertCoupon($fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];

    }

    /**
    *@ 쿠폰 수정
    **/
    public function updateCoupon(Request $params) {

        $requestParams = $params->all();

        $fieldsets = makeFieldset($this->couponRepository->useFields,$requestParams);

        $ptOutData = null;
        $ptInData = null;
        switch($requestParams['ptType']) {
            case 'single':
                $ptInData = $requestParams['selectedProductList'];
                break;
            case 'category':
                $ptInData = $requestParams['selectedCategoryList'];
                $ptOutData = $requestParams['selectedProductList'];
                break;
            case 'brand':
                $ptInData = $requestParams['selectedBrandList'];
                $ptOutData = $requestParams['selectedProductList'];
                break;
            case 'all':
               $ptOutData = $requestParams['selectedProductList'];
               break;
        }

        $fieldsets['ptInData'] = $ptInData;
        $fieldsets['ptOutData'] = $ptOutData;
        $id = $this->couponRepository->updateCoupon($params->input('id'),$fieldsets);
        if($id) {
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];

    }

    /**
    *@ 쿠폰 발행 목록
    **/
    public function getCouponPublishList(Request $params) {
       $requestParams = $params->all();
       $data['couponList'] = $this->couponRepository->getCouponSimpleList();
       $data['publishList'] = $this->couponRepository->getCouponPublishList($requestParams,$params->input('limit'));
       return $data;
    }
    /**
    *@ 쿠폰 발행 목록 (데이타만)
    **/
    public function getCouponPublishDataList(Request $params) {
       $requestParams = $params->all();
       $data = $this->couponRepository->getCouponPublishList($requestParams,$params->input('limit'));
       return $data;
    }

}
