<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingDeliveryRepository;
use App\Repositories\Repository\Api\Core\CoreApiCartRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInventoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductAddInfoRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInfoNoticeRepository;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class CoreProductService {

    protected $productRepository;
    protected $productInventoryRepository;
    protected $productCategoryRepository;
    protected $productBrandRepository;
    protected $deliveryRepository;
    protected $cartRepository;
    protected $addInfoRepository;
    protected $infoNoticeRepository;

    public function __construct(CustomizeProductRepository $productRepository,
                                CustomizeProductCategoryRepository $productCategoryRepository,
                                CustomizeProductBrandRepository $productBrandRepository,
                                CustomizeSettingDeliveryRepository $deliveryRepository,
                                CoreApiCartRepository $cartRepository,
                                CustomizeSettingSiteRepository $siteRepository,
                                CustomizeProductInventoryRepository $productInventoryRepository,
                                CustomizeProductAddInfoRepository $addInfoRepository,
                                CustomizeProductInfoNoticeRepository $infoNoticeRepository) {

        $this->productRepository = $productRepository;
        $this->productInventoryRepository = $productInventoryRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->cartRepository = $cartRepository;
        $this->addInfoRepository = $addInfoRepository;
        $this->infoNoticeRepository = $infoNoticeRepository;

        //$data = $siteRepository->getSiteInfoByField('images');
        /**
         *이미지 사이즈 정보 가져오기
         *   상품목록 이미지 => plist / base width: 640, height: 640},
         *   상품후기 목록 이미지 =>prlist / base width: 640, height: 640},
         *   게시판 목록 이미지 =>blist / base width: 640, height: 640},
         *

        if(!empty($data)) {
            if($data->images) {
                $imageSizeInfo = json_decode($data->images);
                if(!empty($imageSizeInfo->pdetail)) {
                    $this->productRepository->detailImgSize['width'] = $imageSizeInfo->pdetail->width;
                    $this->productRepository->detailImgSize['height'] =$imageSizeInfo->pdetail->height;
                }


                if(!empty($imageSizeInfo->plist)) {
                    $this->productRepository->listImgSize['width'] = $imageSizeInfo->plist->width;
                    $this->productRepository->listImgSize['height'] =$imageSizeInfo->plist->height;
                }
            }
        }
        */
    }

    // 상품 목록( 모든 데이타를 가져옴)
    public function getProductList(Request $params) {


        $requestParams = $params->all();
        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();
        $data['productList'] = $this->_getProductDataList($requestParams);

         return $data;
    }
    // 상품 데이타 목록
    public function getProductDataList(Request $params) {
        $requestParams = $params->all();
        $data = $this->_getProductDataList($requestParams);
        return $data;
    }
    private function _getProductDataList($params) {


        return $this->productRepository->getProductList($params,$params['limit']);

    }
    // 상품 등록 수정시 기본정보 및 상품정보 가져오기
    public function getRegistInfo(Request $params) {


        $id = $params->input('id');
        $data = [];

        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();
        $data['deliveryList'] = $this->deliveryRepository->getDeliveryUseList();
        $data['detailImgSize'] = $this->productRepository->detailImgSize;
        $data['listImgSize'] =  $this->productRepository->listImgSize;
        $data['addInfos'] = $this->addInfoRepository->getAddInfoUseList();

        if($id) {
            $data['product'] = $this->productRepository->getProductInfo($id);
            if($data['product']->relUse=='yes') {
                if(($data['product']->relType=='single') || ($data['product']->relType!='single' && $data['product']->relDeny=='yes')) {
                    $relProductIds = json_decode($data['product']->relProducts);
                    $data['product']->relProductList = $this->productRepository->getProductListWithSaleByIds($relProductIds);
                }

            }
            $data['optionList'] = $this->productRepository->getOptionInfoByPid($id);
            $data['inventoryInfo'] =  $this->productInventoryRepository->getInventoryListByPid($data['product']->id);
        }

        return $data;
    }

    // 상품 등록
    public function insertProduct(Request $params) {

        $requestParams = $params->all();

        $fieldsets = makeFieldset($this->productRepository->useFields,$requestParams);
        $datailImgData = [];
        $uploadParams = [];
        $uploadParams['type'] = ['image'];
        $uploadParams['resize'] = $this->productRepository->detailImgSize;
        foreach($requestParams['detailImgs'] as $key=>$val) {
            $fileIndex = $key+1;
            $imgName = uploadFile($params,'detailImg'.$fileIndex,$this->productRepository->filePath,$uploadParams);

            if(empty($imgName)) {
                return ['status'=>'message','data'=>'wrong_detailimg'];
            }
            $datailImgData[] = $this->productRepository->imgUrl.$imgName;
        }
        $fieldsets['detailImgs'] = json_encode($datailImgData);
        $uploadParams = [];
        $uploadParams['type'] = ['image'];
        $uploadParams['resize'] = $this->productRepository->listImgSize;
        $listImgName = uploadFile($params,'listImg',$this->productRepository->filePath,$uploadParams);
        if(empty($listImgName)) {
            return ['status'=>'message','data'=>'wrong_listimg'];
        }
        $fieldsets['listImg'] = $this->productRepository->imgUrl.$listImgName;
        $fieldsets['pcode'] = $params->input('pcode');
        $fieldsets['gamt'] = $params->input('gamt');

        $info = $this->productRepository->insertProduct($fieldsets);
        if($info &&  $info->id) {
            if(!empty($params->input('pInfoNoti'))) {
                $pInfoNoti = json_decode($params->input('pInfoNoti'));
                if($pInfoNoti->infoNotiRegist=='yes') {
                    $pInfoNoti->pid = $info->id;
                    $pInfoNoti->pname = $info->pname;
                    $this->updateProductInfoNotice($pInfoNoti);
                }
            }


            $inventoryParams = [];
            $inventoryParams['pid'] = $info->id;
            $inventoryParams['optionUse'] = $params->input('optionUse');

            if($params->input('optionUse') == 'yes') {
                $optionInfo = json_decode($params->input('optionInfo'));
                $singleOptionRequired = false;
                foreach($optionInfo->optionList as $option) {
                    $optParams = [];
                    $optParams['pid'] = $info->id;
                    if($params->input('optionType') == 'single') {
                         $optParams['option_name'] = $option->name;
                         $optParams['orequired'] = ($option->required == 'Y')?'Y':'N';
                         if($optParams['orequired']=='Y') {
                            $singleOptionRequired = true;
                         }

                         foreach($option->subList as $subOption) {
                            $optParams['option_code'] = $subOption->code;
                            $optParams['name'] = $subOption->name;
                            $optParams['price'] = (int)$subOption->price;
                            $optParams['dcprice'] = (int)$subOption->dcprice;
                            $optParams['amt'] = (int)$subOption->amt;
                            $optParams['ouse'] = $subOption->use;
                           // $optParams['manger_code'] = ($subOption->manager_code)?$subOption->manager_code:'';

                            $optFieldsets = makeFieldset($this->productRepository->optionUseFields,$optParams);
                            $optionInfo = $this->productRepository->insertProductOption($optFieldsets);

                            // 재고정보 저장
                            $inventoryParams['oid'] = $optionInfo->id;
                            $inventoryParams['addAmt'] = (int)$subOption->amt;
                             //$inventoryParams['manger_code'] = ($subOption->manager_code)?$subOption->manager_code:'';
                            $this->inventoryInsert($inventoryParams);

                         }

                    } else {
                        $optParams['orequired'] = 'Y';
                        $optParams['option_name'] = 'name';
                        $optParams['option_code'] = $option->code;
                        $optParams['name'] = @implode(',',$option->optNames);
                        $optParams['price'] = (int)$option->price;
                        $optParams['dcprice'] = (int)$option->dcprice;
                        $optParams['amt'] = (int)$option->amt;
                        $optParams['ouse'] = $option->use;
                        //$optParams['manger_code'] = ($option->manager_code)?$option->manager_code:'';

                        $optFieldsets = makeFieldset($this->productRepository->optionUseFields,$optParams);
                        $optionInfo = $this->productRepository->insertProductOption($optFieldsets);

                        // 재고정보 저장
                        $inventoryParams['oid'] = $optionInfo->id;
                        $inventoryParams['addAmt'] = (int)$option->amt;
                        //$inventoryParams['manger_code'] = ($option->manager_code)?$option->manager_code:'';
                        $this->inventoryInsert($inventoryParams);
                    }


                }
                if($params->input('optionType') == 'single' && !$singleOptionRequired) {
                    // 재고정보 저장
                    $inventoryParams = [];
                    $inventoryParams['pid'] = $info->id;
                    $inventoryParams['optionUse'] = $params->input('optionUse');
                    $inventoryParams['addAmt'] = $params->input('gamt');  // 판매가능한 재고
                    $this->inventoryInsert($inventoryParams);
                }
            } else {
                // 재고정보 저장
                $inventoryParams['addAmt'] = $params->input('gamt');  // 판매가능한 재고
                $this->inventoryInsert($inventoryParams);
            }
            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$info];

    }

    // 상품 수정
    public function updateProduct(Request $params) {

        $requestParams = $params->all();
        if(!$requestParams['id'])return ['status'=>'fail','data'=>''];
        if($requestParams['id']=='newProduct')return ['status'=>'fail','data'=>''];

        $productInfo = $this->productRepository->getProductInfo($requestParams['id']);
        if(empty($productInfo->id))return ['status'=>'fail','data'=>''];



        $fieldsets = makeFieldset($this->productRepository->useFields,$requestParams);

        $datailImgData = [];
        $uploadParams = [];
        $uploadParams['type'] = ['image'];
        $uploadParams['resize'] = $this->productRepository->detailImgSize;
        foreach($requestParams['detailImgs'] as $key=>$val) {
            $fileIndex = $key+1;
            if($params->input('isDetailImg'.$fileIndex)) {
                $datailImgData[] = $params->input('isDetailImg'.$fileIndex);
            } else {
                $imgName = uploadFile($params,'detailImg'.$fileIndex,$this->productRepository->filePath,$uploadParams);
                if(empty($imgName)) {
                    return ['status'=>'message','data'=>'wrong_detailimg'];
                }
                $datailImgData[] = $this->productRepository->imgUrl.$imgName;
            }
        }
        $fieldsets['detailImgs'] = json_encode($datailImgData);
        if($params->file('listImg')) {
            $uploadParams = [];
            $uploadParams['type'] = ['image'];
            $uploadParams['resize'] = $this->productRepository->listImgSize;
            $listImgName = uploadFile($params,'listImg',$this->productRepository->filePath,$uploadParams);
            if(empty($listImgName)) {
                return ['status'=>'message','data'=>'wrong_listimg'];
            }
            $fieldsets['listImg'] = $this->productRepository->imgUrl.$listImgName;
        }
        if($params->input('optionUse') == 'no' && !empty($params->input('addAmt'))) {
            $fieldsets['gamt'] = $productInfo->gamt + $params->input('addAmt');
        }
        $id = $this->productRepository->updateProduct($requestParams['id'],$fieldsets);
        if($id) {
            if(!empty($params->input('pInfoNoti'))) {
                $pInfoNoti = json_decode($params->input('pInfoNoti'));
                if($pInfoNoti->infoNotiRegist=='yes') {
                    $pInfoNoti->pid = $id;
                    $pInfoNoti->pname = $params->input('pname');
                    $this->updateProductInfoNotice($pInfoNoti);
               }
            }
            $inventoryParams = [];

            if($params->input('optionUse') == 'yes') {
                if($params->input('optionRemove')=='yes') {
                    $this->cartRepository->deleteCartByPid($id);

                    $inventoryParams['pid'] = $id;
                    $inventoryParams['optionUse'] = $params->input('optionUse');
                }

                $optionInfo = json_decode($params->input('optionInfo'));
                $singleOptionRequired = false;

                foreach($optionInfo->optionList as $option) {
                    $optParams = [];
                    $optParams['pid'] = $id;
                    if($params->input('optionType') == 'single') {
                         $optParams['option_name'] = $option->name;
                         $optParams['orequired'] = ($option->required == 'Y')?'Y':'N';
                         if($optParams['orequired']=='Y') {
                            $singleOptionRequired = true;
                         }
                         foreach($option->subList as $subOption) {
                            $optParams['option_code'] = $subOption->code;
                            $optParams['name'] = $subOption->name;
                            $optParams['price'] = (int)$subOption->price;
                            $optParams['dcprice'] = (int)$subOption->dcprice;
                            $optParams['ouse'] = $subOption->use;
                            //$optParams['manger_code'] = ($subOption->manager_code)?$subOption->manager_code:'';



                            $optFieldsets = makeFieldset($this->productRepository->optionUseFields,$optParams);
                            if($params->input('optionRemove')=='yes') {
                                $optFieldsets['amt'] = (int)$subOption->amt;
                                $optionInfo = $this->productRepository->insertProductOption($optFieldsets);

                                // 재고정보 저장
                                //$inventoryParams['manger_code'] = ($option->manager_code)?$option->manager_code:'';
                                $inventoryParams['oid'] = $optionInfo->id;
                                $inventoryParams['addAmt'] =(int)$subOption->amt;
                                $this->inventoryInsert($inventoryParams);


                            } else {
                                if($subOption->id) {
                                    if(!empty($subOption->addAmt)) {
                                        $optFieldsets['amt'] = (int)$subOption->amt + (int)$subOption->addAmt;
                                    }
                                    $this->productRepository->updateProductOption($subOption->id,$optFieldsets);
                                    //재고 수정
                                    if(!empty($subOption->addAmt)) {
                                        $inventoryParams['addAmt'] = (int)$subOption->addAmt;
                                        $inventoryParams['oid'] = $subOption->id;

                                        $this->inventoryUpdate($inventoryParams);
                                    }
                                }
                            }
                         }

                    } else {
                        $optParams['orequired'] = 'Y';
                        $optParams['option_name'] = 'name';
                        $optParams['option_code'] = $option->code;
                        $optParams['name'] = @implode(',',$option->optNames);
                        $optParams['price'] = (int)$option->price;
                        $optParams['dcprice'] = (int)$option->dcprice;
                        $optParams['ouse'] = $option->use;
                       // $optParams['manger_code'] = ($option->manager_code)?$option->manager_code:'';

                        $optFieldsets = makeFieldset($this->productRepository->optionUseFields,$optParams);
                        if($params->input('optionRemove')=='yes') {
                            $optFieldsets['amt'] = (int)$option->amt;
                            $optionInfo = $this->productRepository->insertProductOption($optFieldsets);
                            // 재고정보 저장
                            $inventoryParams['oid'] = $optionInfo->id;
                            $inventoryParams['addAmt'] = (int)$option->amt;

                            //$inventoryParams['manger_code'] = ($option->manager_code)?$option->manager_code:'';
                            $this->inventoryInsert($inventoryParams);
                        } else {
                             if($option->id) {
                                if(!empty($option->addAmt)) {
                                    $optFieldsets['amt'] = (int)$option->amt + (int)$option->addAmt;
                                }

                                $this->productRepository->updateProductOption($option->id,$optFieldsets);
                                 //재고 수정
                                if(!empty($option->addAmt)) {
                                    $inventoryParams['addAmt'] = (int)$option->addAmt;
                                    $inventoryParams['oid'] = $option->id;
                                    $this->inventoryUpdate($inventoryParams);
                                }
                             }

                        }
                    }
                }
                if($params->input('optionType') == 'single' && !$singleOptionRequired && !empty($params->input('addAmt'))) {
                    // 재고정보 수정
                    $inventoryParams = [];
                    $inventoryParams['pid'] = $id;
                    $inventoryParams['addAmt'] = (int)$params->input('addAmt');
                    $this->inventoryUpdate($inventoryParams);
                }

                // 상품테이블에 재고 정보 저장
                $addAmt = (!empty($params->input('addAmt')))?$params->input('addAmt'):0;
                $this->finalUpdateProductAmt($id,$params->input('optionType'), $addAmt);
            } else {

                if(!empty($params->input('addAmt'))) {
                    //재고 수정
                    $inventoryParams = [];
                    $inventoryParams['pid'] = $id;
                    $inventoryParams['addAmt'] = (int)$params->input('addAmt');
                    $this->inventoryUpdate($inventoryParams);
                }

            }

            $status = 'success';
        } else $status = 'error';

        return ['status'=>$status,'data'=>$id];

    }

    private function finalUpdateProductAmt(int $pid,string $optionType,int $addAmt) {
           $maxAmt = 0;
           $updateAmtFlag = false;
           $optionList = $this->productRepository->getOptionInfoByPid($pid);
           foreach($optionList as $option) {
                if($optionType == 'multi') { // 조합형 옵션 (필수옵션임)
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
           $params = [];
           if($updateAmtFlag) {
                $params['gamt'] = $maxAmt;
                $this->productRepository->updateProduct($pid,$params);
           } else if($addAmt>0) {
                $params['gamt'] = $addAmt;
                $this->productRepository->updateProduct($pid,$params);

           }


    }

    /// 상품 재고  수정
    private function inventoryUpdate($params) {


        if(!empty($params['oid'])) {
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByOid($params['oid']);
        } else if(!empty($params['pid'])) {
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByPid($params['pid']);
        } else {
            return;
        }
        if(!$inventoryInfo)return;

        $addAmt = $params['addAmt'];

        $inventoryParams = [];
        $able_amt = $inventoryInfo->able_amt + $addAmt;  // 판매가능한 재고
        $total_amt = $inventoryInfo->total_amt + $addAmt; // 총 재고

        $inventoryParams['able_amt'] = $able_amt;
        $inventoryParams['total_amt'] = $total_amt;
        //$inventoryParams['manger_code'] = $params->input('manger_code'); // 관리코드
        $this->productInventoryRepository->updateInventoryProduct($inventoryParams,$inventoryInfo->id);
        // 이벤트 정보 (재고히스토리)
        $eventParams['ivt_id'] = $inventoryInfo->id;
        $eventParams['type'] = 'up';
        $eventParams['content'] = ['addAmt'=>$addAmt,'able_amt'=>$able_amt,'total_amt'=>$total_amt];
        \Event::dispatch(new \App\Events\InventoryHistoryEvent($eventParams));
    }

    /// 상품 재고  저장
    private function inventoryInsert(array $params) {

        $inventoryParams['pid'] = $params['pid'];
        if(!empty($params['oid']))$inventoryParams['oid'] = $params['oid'];

        $inventoryParams['optionUse'] = $params['optionUse'];
        $inventoryParams['disable_amt'] = 0;
        $inventoryParams['able_amt'] = $params['addAmt'];  // 판매가능한 재고
        $inventoryParams['total_amt'] = $params['addAmt']; // 총 재고
        $inventoryParams['sale_amt'] = 0; // 판매 갯수
        //$inventoryParams['manger_code'] = $params->input('manger_code'); // 관리코드
        $this->productInventoryRepository->insertProductInventory($inventoryParams);

    }
    ///  상품 정보고시 데이타 저장/수정
    private function updateProductInfoNotice($pInfoNoti) {
        $fieldsets['datas'] = json_encode($pInfoNoti->infoNotices);
        $fieldsets['code'] = $pInfoNoti->infoNotiId;
        $fieldsets['pid'] = $pInfoNoti->pid;
        $fieldsets['pname'] = $pInfoNoti->pname;
        $this->infoNoticeRepository->updateProductInfoNotice($fieldsets);
    }
    /// 상품 상세 이미지 임시저장 /////
    public function insertTempImage(Request $request) {
        $params['type'] = 'image';
        $imgName = uploadFile($request,'image',$this->productRepository->detailImgPath,$params);
        $imgUrl = $this->productRepository->editorImgUrl.$imgName;

        return ['status'=>'success','data'=>$imgUrl];
    }

    public function getProductInfoNoticeList(Request $request) {
        return $this->infoNoticeRepository->getProductInfoNoticeList();
    }

    public function deleteProductInfoNotice(Request $request) {
        $this->infoNoticeRepository->deleteProductInfoNotice($request->input('id'));
        return ['status'=>'success','data'=>''];
    }

    private function developerRemoveAll() {
        return;
         $table[] = config('tables.cart');
         $table[] = config('tables.product');
         //$table[] = config('tables.productAddInfo');
         //$table[] = config('tables.productBrand');
         //$table[] = config('tables.productCategory');
         $table[] = config('tables.productInfoNotice');
         $table[] = config('tables.productInquire');
         $table[] = config('tables.productInventory');
         $table[] = config('tables.inventoryHistory');
         $table[] = config('tables.productOption');
         $table[] = config('tables.wish');
         $table[] = config('tables.coupon');
         $table[] = config('tables.couponPublish');
         $table[] = config('tables.point');
         $table[] = config('tables.order');
         $table[] = config('tables.orderClaim');
         $table[] = config('tables.orderClaimProduct');
         $table[] = config('tables.orderHistory');
         $table[] = config('tables.orderProduct');
         $table[] = config('tables.orderReview');
         foreach($table as $tbl) {
             DB::table($tbl)->delete();
         }



    }
}
