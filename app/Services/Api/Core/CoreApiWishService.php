<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiWishRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductCategoryRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiWishService extends CoreApiAuthHeader {

    protected $productRepository;
    protected $wishRepository;
    protected $categoryRepository;

    public function __construct(Request $request,
                                CustomizeApiProductCategoryRepository $categoryRepository,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiWishRepository $wishRepository) {
        parent::__construct($request);

        $this->productRepository = $productRepository;
        $this->wishRepository = $wishRepository;
        $this->categoryRepository = $categoryRepository;

    }

    // 관심 목록
    public function getMyWishList() {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $list = $this->wishRepository->getWishListByAll($this->isLoginInfo->id);
        $productPids = [];
        foreach($list as $val) {
            switch($val->type) {
                case 'product':
                    $productPids[] = $val->pid;
                    break;
            }
        }
        $data = [];
        if(count($productPids)>0) {
            $data['productList'] = $this->productRepository->getProductListSaleByIds($productPids);
        }
        $data['categoryList'] = $this->categoryRepository->getCategoryUseList();

        return ['status'=>'success','data'=>$data];
    }
    // 관심 추가/삭제
    public function updateWish(array $params) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        switch($params['type']) {
            case 'product':
                $targetRow = $this->productRepository->getProductInfo($params['pid']);
                if(!$targetRow->id) {
                    return ['status'=>'error','data'=>''];
                }
            break;
        }
        $row = $this->wishRepository->getWishInfoByPidTypeUser($params['pid'],$params['type'],$this->isLoginInfo->id);
        $data = [];
        if($row) {
            $result = $this->wishRepository->deleteWish($row->id);
            $data['rtype'] = 'delete';
        } else {
            $params['user_id'] = $this->isLoginInfo->id;
            $fieldset = makeFieldset($this->wishRepository->useFields,$params);
            $result = $this->wishRepository->insertWish($fieldset);
            $data['rtype'] = 'insert';
        }

        if($result) {
            $status = 'success';
            $updParams['wish'] = $this->wishRepository->getWishTotalByPidType($params['pid'],$params['type']);
            $data['total'] = $updParams['wish'];
            switch($params['type']) {
                case 'product':
                    $this->productRepository->updateProduct($targetRow->id,$updParams);
                break;
            }
        } else {
            $status = 'fail';
        }
        return ['status'=>$status,'data'=>$data];
    }

}
