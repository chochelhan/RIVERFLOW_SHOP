<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductCategoryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductBrandRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductInquireRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiWishRepository;
use App\Repositories\Repository\Base\BaseSettingDeliveryRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiProductService extends CoreApiAuthHeader {

    protected $productRepository;
    protected $categoryRepository;
    protected $brandRepository;
    protected $wishRepository;
    protected $deliveryRepository;
    protected $productInquireRepository;
    protected $loginInfo = [];

    public function __construct(Request $request,
                                BaseSettingDeliveryRepository $deliveryRepository,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiProductCategoryRepository $categoryRepository,
                                CustomizeApiProductBrandRepository $brandRepository,
                                CustomizeApiProductInquireRepository $productInquireRepository,
                                CustomizeApiWishRepository $wishRepository) {
        parent::__construct($request);


        $this->deliveryRepository = $deliveryRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        $this->wishRepository = $wishRepository;

        $this->productInquireRepository = $productInquireRepository;

        if($this->isLoginInfo) {
            $this->loginInfo = ['type'=>'member','info'=>$this->isLoginInfo];
        } else {
            $this->loginInfo = ['type'=>'noMember','id'=>$this->noMemberId];
        }
    }

    // 상품 목록( 모든 데이타를 가져옴)
    public function getProductList(Request $params) {

        $requestParams = $params->all();
         $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
        if(!empty($requestParams['category'])) {
           $categoryInfo = $this->categoryRepository->getCategoryUseListByParent($requestParams['category']);
           if(!empty($categoryInfo)) {
                $data['categoryInfo'] = $categoryInfo[0];
                $data['categoryDataList'] = $categoryInfo[1];
           }
        }
        $data['imageset'] = $this->siteInfos['images'];
        $data['brandList'] = $this->brandRepository->getBrandUseList();
        $data['productList'] = $this->_getProductDataList($requestParams);

        return $data;
    }
    // 상품 데이타 목록
    public function getProductDataList(Request $params) {
        $requestParams = $params->all();
        $data = $this->_getProductDataList($requestParams);
        return $data;
    }
    private function _getProductDataList(array $params) {

        if(!empty($this->isLoginInfo)) {
            $params['user_id'] = $this->isLoginInfo->id;
        }
        $params['limit'] = (empty($params['limit']))?12:$params['limit'];

        return $this->productRepository->getProductList($params,$params['limit']);

    }

    // 상품 상세
    public function getProductInfo(Request $request) {

        $id = $request->input('id');
        if(!empty($request->input('category'))) {
            $category = $request->input('category');
        } else {
            $category = 'default';
        }
        $productInfo = $this->productRepository->getProductInfo($id);
        if(empty($productInfo)) {
            return ['status'=>'fail','data'=>''];
        }
        if(!empty($productInfo->brandId)) {
            $brandInfo = $this->brandRepository->getBrandInfo($productInfo->brandId);
            $productInfo->brandName = $brandInfo->bname;
        }

        if($category == 'default') {
            $searchCategorys = explode(',',$productInfo->category1);
            $baseCategory = $productInfo->category1;
        } else {
            $searchFlag = searchCategory($productInfo->category1,$category);
            if(!$searchFlag) {
                $searchFlag = searchCategory($productInfo->category2,$category);
                if(!$searchFlag) {
                    $searchFlag = searchCategory($productInfo->category3,$category);
                    if($searchFlag) {
                        $searchCategorys = explode(',',$productInfo->category3);
                        $baseCategory = $productInfo->category3;
                    }
                } else  {
                    $searchCategorys = explode(',',$productInfo->category2);
                    $baseCategory = $productInfo->category2;
                }
            } else  {
                $searchCategorys = explode(',',$productInfo->category1);
                $baseCategory = $productInfo->category1;
            }
        }
        if($searchCategorys) {
            $categoryNames = [];
            foreach($searchCategorys as $cid) {
                $cateInfo = $this->categoryRepository->getCategoryInfo($cid);
                $categoryNames[] = $cateInfo->cname;
            }
            $data['categoryNames'] = $categoryNames;
            $data['baseCategory'] = $baseCategory;
        }
        if(!empty($this->isLoginInfo)) {
            $wishInfo = $this->wishRepository->getWishInfoByPidTypeUser($id,'product',$this->isLoginInfo->id);
            if($wishInfo) {
                $productInfo->myWish = 1;
            }
        }

        if($productInfo->optionUse == 'yes') {
            $data['optionList'] = $this->productRepository->getUseOptionInfoByPid($id);
            $productInfo->optionInfo = json_decode($productInfo->optionInfo);

        }
        if($productInfo->serviceType=='normal' && $productInfo->deliveryId) {
            $data['deliveryInfo'] = $this->deliveryRepository->getDeliveryInfo($productInfo->deliveryId);
        }
        $data['productInfo'] = $productInfo;
        $data['pointInfo'] = $this->siteInfos['points'];
        return ['status'=>'success','data'=>$data];
    }

    // 상품과 관련된 상품
    public function getProductRelationList(Request $request) {

        $pid = $request->input('pid');
        $productInfo = $this->productRepository->getProductInfo($pid);
        if(empty($productInfo)) {
                   return ['status'=>'fail','data'=>''];
        }
        $data['imageset'] = $this->siteInfos['images'];
        $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
        $status = 'error';
        if($productInfo->relUse == 'yes') {
            $denyProduct = false;
            $listProduct = false;
            switch($productInfo->relType) {
                case 'category':
                    $params['category'] = $productInfo->category1;
                    if($productInfo->relDeny=='yes') {
                        $denyProduct = true;
                    }
                break;
                case 'brand':
                    $params['brandId'] = $productInfo->brandId;
                    if($productInfo->relDeny=='yes') {
                        $denyProduct = true;
                    }
                break;
                case 'single':
                    $listProduct = true;
                break;
            }
            if($listProduct && $productInfo->relProducts) {
                $relProdcutIds = json_decode($productInfo->relProducts);
                $data['productList'] = $this->productRepository->getProductListWithSaleByIds($relProdcutIds);
            } else {
                    $denyProdcutIds = [];
                if($denyProduct && $productInfo->relProducts) {
                    $relProdcutIds = json_decode($productInfo->relProducts);
                    foreach($relProdcutIds as $id) {
                        $denyProdcutIds[$id] = $id;
                    }
                }


                $params['limit'] = 40;
                $productList = $this->productRepository->getProductList($params,$params['limit']);
                $infoDatas = [];
                foreach($productList as $infoData) {
                    if($productInfo->id == $infoData->id)continue;
                    if(!empty($denyProdcutIds[$infoData->id]))continue;
                    $infoDatas[] = $infoData;
                }
                $data['productList'] = $infoDatas;
            }
            $status = 'success';
        }
        return ['status'=>$status,'data'=>$data];
    }



    // 상품 문의 저장
    public function insertProductInquire(Request $params) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }

        if(!$params->has(['pid','subject','category'])) {
            return ['status'=>'emptyField','data'=>''];
        }
        $requestParams = $params->all();
        $requestParams['user_id'] = $this->isLoginInfo->id;
        $requestParams['name'] = $this->isLoginInfo->name;
        $requestParams['content'] = '답변 준비중';

        $fieldset = makeFieldset($this->productInquireRepository->useFields,$requestParams);
        $data = $this->productInquireRepository->insertProductInquire($fieldset);
        if($data) {
            $status = 'success';
        } else {
            $status = 'fail';
        }
        return ['status'=>$status,'data'=>$data];
    }

    // 상품 문의 목록
    public function getProductInquireList(Request $request) {

        $data['qnaList'] = $this->productInquireRepository->getProductInquireList($request->input('pid'));
        $data['categorys'] = config('qna.categorys');
        return ['status'=>'success','data'=>$data];
    }



}
