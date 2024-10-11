<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInventoryRepository;


use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class CoreInventoryService {

    protected $productRepository;
    protected $productCategoryRepository;
    protected $productBrandRepository;
    protected $inventoryRepository;

    protected $pidList = []; // 재고 (상품테이블) 최종 계산용
    public function __construct(CustomizeProductRepository $productRepository,
                                CustomizeProductCategoryRepository $productCategoryRepository,
                                CustomizeProductBrandRepository $productBrandRepository,
                                CustomizeProductInventoryRepository $inventoryRepository) {

        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->inventoryRepository = $inventoryRepository;

    }

    // 상품재고 목록(미옵션,단독형옵션 모든 데이타를 가져옴)
    public function getProductList(Request $params) {

        $requestParams = $params->all();
        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();
        $data['productList'] = $this->productRepository->getProductInventoryList($requestParams,$requestParams['limit']);

         return $data;
    }
    // 상품재고(미옵션,단독형옵션) 데이타 목록
    public function getProductDataList(Request $params) {
        $requestParams = $params->all();
        $data = $this->productRepository->getProductInventoryList($requestParams,$requestParams['limit']);
        return $data;
    }

    // 상품재고 목록(조합형옵션 모든 데이타를 가져옴)
    public function getOptionList(Request $params) {

        $requestParams = $params->all();
        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();
        $data['productList'] = $this->productRepository->getOptionInventoryList($requestParams,$requestParams['limit']);

         return $data;
    }
    // 상품재고(조합형옵션) 데이타 목록
    public function getOptionDataList(Request $params) {
        $requestParams = $params->all();
        $data = $this->productRepository->getOptionInventoryList($requestParams,$requestParams['limit']);
        return $data;
    }

    // 재고 히스토리 목록
    public function getInventoryHistoryList(Request $params) {
        $ivtId = $params->input('ivtId');
        $data = $this->inventoryRepository->getInventoryHistoryList($ivtId);
        return $data;
    }

    // 재고 변경
    public function updateInventoryProduct(Request $params) {
        $ids = $params->input('ids');
        foreach($ids as $id) {
            $updateParams = [];
            $inventoryInfo = $this->inventoryRepository->getInventoryInfo($id);
            $updateParams['type'] = $params->input('type');
            if($updateParams['type'] == 'up') {
                $able_amt = $inventoryInfo->able_amt + $params->input('amt');  // 판매가능한 재고
                $total_amt = $inventoryInfo->total_amt + $params->input('amt'); // 총 재고
            } else {
                $able_amt = $inventoryInfo->able_amt - $params->input('amt');  // 판매가능한 재고
                $total_amt = $inventoryInfo->total_amt - $params->input('amt'); // 총 재고
                if($able_amt<1)$able_amt = 0;
                if($total_amt<1)$total_amt = 0;

            }
            $updateParams['able_amt'] = $able_amt;
            $updateParams['total_amt'] = $total_amt;

            $resultId = $this->inventoryRepository->updateInventoryProduct($updateParams,$id);
            if($resultId) {

                $this->updateProductAmt($inventoryInfo,$able_amt);
                // 이벤트 정보 (재고히스토리)
                $eventParams = [];
                $eventParams['ivt_id'] = $id;
                $eventParams['type'] = $params->input('type');
                $eventParams['content'] = ['addAmt'=>$params->input('amt'),'able_amt'=>$able_amt,'total_amt'=>$total_amt];
                \Event::dispatch(new \App\Events\InventoryHistoryEvent($eventParams));
            }
        }
        if($resultId) {
            $this->finalUpdateProductAmt();

            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];
    }

    // 상품의 재고 정보를 수정한다
    private function updateProductAmt($inventoryInfo,int $able_amt) {
        $pid = $inventoryInfo->pid;
        if($inventoryInfo->optionUse == 'yes') {
            if(empty($inventoryInfo->oid)) {
                $params['gamt'] = $able_amt;
                $this->productRepository->updateProduct($pid,$params);
            } else {
                $oid = $inventoryInfo->oid;
                $params['amt'] = $able_amt;
                $this->productRepository->updateProductOption($oid,$params);
                $this->pidList[$pid] = $pid;
            }
        } else {
            $params['gamt'] = $able_amt;
            $this->productRepository->updateProduct($pid,$params);
        }


    }

    private function finalUpdateProductAmt() {
        foreach($this->pidList as $pid) {
           $productInfo = $this->productRepository->getProductInfo($pid);
           $maxAmt = 0;
           $updateAmtFlag = false;
           $optionList = $this->productRepository->getOptionInfoByPid($pid);
           foreach($optionList as $option) {
                if($productInfo->optionType == 'multi') { // 조합형 옵션 (필수옵션임)
                    if($option->ouse=='Y') {
                        if($option->amt > $maxAmt) {
                            $maxAmt = $option->amt;
                        }
                    }
                    $updateAmtFlag = true;
                } else {
                    if($option->orequired=='Y') { // 필수옵션 체크
                        $maxAmt = $option->amt;
                        $updateAmtFlag = true;
                    }
                }

           }
           if($updateAmtFlag) {
                $params = [];
                $params['gamt'] = $maxAmt;
                $this->productRepository->updateProduct($pid,$params);
           }
        }

    }
}
