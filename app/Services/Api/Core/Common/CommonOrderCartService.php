<?php

namespace App\Services\Api\Core\Common;
use App\Repositories\Repository\Api\Customize\CustomizeApiCartRepository;

class CommonOrderCartService {

    protected $cartRepository;

    public function __construct(CustomizeApiCartRepository $cartRepository) {
        $this->cartRepository = $cartRepository;

    }


    public function getCartParseDatas($cartList) {

        $resultDatas = [];
        $totalPrice = 0;
        $totalMaxUsePoint = 0; // 사용가능 최대 적립금(상품금액과 설정에 따라)
        $totalOrderAmt = 0;
        $totalReservePoint = 0; // 총지급 적립금
        $resultPids = [];
        $serviceType = 'service'; // 배송상품군 여부
        foreach($cartList as $data) {

            // 카테고리 이름 가져오기
            $categoryIds = explode(',',$data->category1);
            $data->categoryNames = $this->cartRepository->getCategoryNamesByIds($categoryIds);
            $categoryIds2 = (empty($data->category2))?explode(',',$data->category2):[];
            $categoryIds3 = (empty($data->category3))?explode(',',$data->category3):[];


            if($data->optionUse=='yes' && $data->optionType=='single' && !empty($data->singleOptionInfos)) { // 단독형 옵션에서 옵션정보 있을경우

                $optionInfos = json_decode($data->singleOptionInfos); // 카트에 저장된 옵션정보
                $optIds = [];
                $optAmts = [];
                foreach($optionInfos as $opt) {
                    $optIds[] = $opt->id;
                    $optAmts[$opt->id] = $opt->camt; // 구매수량
                }
                $optionResult = [];
                $optList = $this->cartRepository->getOptionInfoByIds($optIds); // 디비에 저장된 옵션정보

                $optionRequiredFlag = false;
                $optionName = '';
                $optionStatus = '';
                $optionDcprice = '';
                $optionPrice = '';
                $optionSellprice = '';
                $optionId = '';
                $optionTotalPrice = 0;
                $optionGamt = 0;
                foreach($optList as $key=>$option) {

                        $status = 'sale';
                        if($optAmts[$option->id] > $option->amt) { // 구매수량이 재고량 보다 많을때
                            if($option->amt<1) { // 판매가능 재고 수량이 0일때
                                $status = 'soldout'; //
                            } else {
                                $optAmts[$option->id] = $option->amt; // 구매수량을 판매가능 재고와 맞춘다
                            }
                        }
                        $optionResult[$key]['required'] = $option->orequired;
                        $optionResult[$key]['camt'] = $optAmts[$option->id];
                        $optionResult[$key]['opt_id'] = $option->id;
                        $optionResult[$key]['invAmt'] = $option->amt; // 현재고정보
                        $optionResult[$key]['status'] = $status;
                        $optionResult[$key]['dcprice'] = $option->dcprice;
                        $optionResult[$key]['price'] = $option->price;
                        if($status == 'sale') {
                            $price = ($option->dcprice && ($option->dcprice< $option->price))?$option->dcprice:$option->price;
                            $optionResult[$key]['sellprice'] = ($price * $optAmts[$option->id]);
                            $totalPrice = $totalPrice + ($price * $optAmts[$option->id]);
                            $optionTotalPrice = $optionTotalPrice + ($price * $optAmts[$option->id]);
                        }
                        $optionResult[$key]['option_name'] = $option->name;
                        $optionResult[$key]['option_parent_name'] = $option->option_name;

                        if($option->orequired=='Y') {
                            $optionRequiredFlag = true;
                            $optionName = $option->option_name.':'.$option->name;
                            $optionStatus = $status;
                            $optionId = $option->id;
                            $optionDcprice = $option->dcprice;
                            $optionPrice = $option->price;
                            $optionGamt = $option->amt;
                            if($status == 'sale') {
                                $optionSellprice = ($price * $data->camt);

                            } else $optionSellprice = 0;

                        }

                }

                $data->option_name = $optionName;
                if($optionRequiredFlag) {
                    $data->optionRequired = 'Y';
                    $data->status = $optionStatus;
                    $data->optionId = $optionId;
                    $data->dcprice =  $optionDcprice;
                    $data->price = $optionPrice;
                    $data->sellprice = $optionSellprice;
                    $data->gamt = $optionGamt;
                    // 적립금 지급여부
                    $data->reservePoint = $this->checkReservePointByOption($data,$optionTotalPrice);
                    if($optionStatus == 'sale') {
                        if($data->serviceType == 'normal')$serviceType = 'normal';
                        $totalReservePoint = $totalReservePoint + $data->reservePoint;
                        if($data->pointUse != 'no') {
                            $totalMaxUsePoint = $totalMaxUsePoint + $optionTotalPrice;
                        }
                        $totalOrderAmt = $totalOrderAmt + $data->camt;
                        $resultPids[$data->pid] = ['pid'=>$data->pid,
                                                    'category'=>[$categoryIds,$categoryIds2,$categoryIds3],
                                                      'brandId'=>$data->brandId,
                                                    'priceInfo'=>$data->sellprice,
                                                    ];

                     }

                } else {
                    $status = 'sale';
                    if($data->camt > $data->gamt) {
                        if($data->gamt<1) {
                            $status = 'soldout'; //
                        } else {
                            $data->camt = $data->gamt;
                        }
                    }
                    $price = ($data->dcprice && ($data->dcprice< $data->price))?$data->dcprice:$data->price;
                    $data->sellprice = ($price * $data->camt);
                    $optionTotalPrice = $optionTotalPrice + ($price * $data->camt);
                    // 적립금 지급여부
                    $data->reservePoint = $this->checkReservePointByOption($data,$optionTotalPrice);
                    $data->status = $status;
                    if($status == 'sale') {
                        if($data->serviceType == 'normal')$serviceType = 'normal';
                        $totalReservePoint = $totalReservePoint + $data->reservePoint;
                        if($data->pointUse != 'no') {
                            $totalMaxUsePoint = $totalMaxUsePoint + $optionTotalPrice;
                        }
                        $totalPrice = $totalPrice + ($price * $data->camt);
                        $totalOrderAmt = $totalOrderAmt + $data->camt;

                        $resultPids[$data->pid] = [ 'pid'=>$data->pid,
                                                     'category'=>[$categoryIds,$categoryIds2,$categoryIds3],
                                                       'brandId'=>$data->brandId,
                                                     'priceInfo'=>$data->sellprice,
                                                     ];
                    }
                    $data->optionRequired = 'N';
                }

                $data->optionResult = $optionResult;
                $resultDatas[] = $data;

            } else  if($data->optionUse=='yes' && $data->optionType=='multi') { // 조합형 옵션
                $optInfo = $this->cartRepository->getOptionInfo($data->option_id);
                if($optInfo) {
                    $status = 'sale';
                    if($optInfo->amt < $data->camt) { // 구매수량이 재고량 보다 많을때
                        if($optInfo->amt<1) { // 판매가능 재고 수량이 0일때
                            $status = 'soldout'; //
                        } else {
                            $data->camt = $optInfo->amt; // 구매수량을 판매가능 재고와 맞춘다
                        }
                    }
                    $data->gamt = $optInfo->amt;
                    $data->status = $status;
                    $data->dcprice = $optInfo->dcprice;
                    $data->price = $optInfo->price;

                    $price = ($data->dcprice && ($data->dcprice< $data->price))?$data->dcprice:$data->price;
                    $data->sellprice = ($price * $data->camt);

                    // 적립금 지급여부
                    $data->reservePoint = $this->checkReservePoint($data);


                    if($status == 'sale') {
                        $totalPrice = $totalPrice + ($price * $data->camt);
                        if($data->pointUse != 'no') {
                            $totalMaxUsePoint = $totalMaxUsePoint + ($price * $data->camt);
                        }

                        $totalOrderAmt = $totalOrderAmt + $data->camt;
                        $resultPids[$data->pid] = [ 'pid'=>$data->pid,
                                                   'category'=>[$categoryIds,$categoryIds2,$categoryIds3],
                                                     'brandId'=>$data->brandId,
                                                   'priceInfo'=>$data->sellprice,
                                                   ];
                        if($data->serviceType == 'normal')$serviceType = 'normal';
                        $totalReservePoint = $totalReservePoint + $data->reservePoint;
                    }
                    $data->option_name = $optInfo->name;
                    $resultDatas[] = $data;

                }


            } else  { // 옵션이 없는 상품
                $status = 'sale';
                if($data->camt > $data->gamt) {
                    if($data->gamt<1) {
                        $status = 'soldout'; //
                    } else {
                        $data->camt = $data->gamt;
                    }
                }

                $price = ($data->dcprice && ($data->dcprice< $data->price))?$data->dcprice:$data->price;
                $data->sellprice = ($price * $data->camt);

                // 적립금 지급여부
                $data->reservePoint = $this->checkReservePoint($data);

                $data->status = $status;
                if($status == 'sale') {
                    $totalPrice = $totalPrice + ($price * $data->camt);
                    $totalOrderAmt = $totalOrderAmt + $data->camt;
                    if($data->pointUse != 'no') {
                        $totalMaxUsePoint = $totalMaxUsePoint + ($price * $data->camt);
                    }

                    $resultPids[$data->pid] = [ 'pid'=>$data->pid,
                                                'category'=>[$categoryIds,$categoryIds2,$categoryIds3],
                                                'brandId'=>$data->brandId,
                                                'priceInfo'=>$data->sellprice,
                                                ];
                    if($data->serviceType == 'normal')$serviceType = 'normal';
                    $totalReservePoint = $totalReservePoint + $data->reservePoint;
                }
                $resultDatas[] = $data;

            }
        }
        return [
                'serviceType'=>$serviceType,
                'data'=>$resultDatas,
                'totalPrice'=>$totalPrice,
                'totalOrderAmt'=>$totalOrderAmt,
                'resultPids'=>$resultPids,
                'totalReservePoint'=>$totalReservePoint,
                'totalMaxUsePoint'=>$totalMaxUsePoint,
                ];

    }

    public function getOrderDeliveryInfo($cartData,$siteInfos) {
        $deliveryInfo = [];
        $checkPrice = '';
        $duseType = (!empty($this->siteInfos))?$this->siteInfos->groupType:'max'; // 배송비가 여러개일대 선택기준
        foreach($cartData['data'] as $data) {
            switch($data->dpriceType) {
                case 'price':// 고정배송비
                    $deliveryPrice = $data->fprice;
                break;
                case 'half':// 조건부
                    if($cartData['totalPrice'] >= $data->oprice) {
                        $deliveryPrice = 0;
                    } else {
                        $deliveryPrice = $data->mprice;
                    }
                break;
                case 'free':// 무료
                    $deliveryPrice = 0;

                break;

            }
            if($data->dmethod == 'direct') { // 직접배송이 섞인 경우 직접배송으로 묶음
                $checkPrice = $deliveryPrice;
                $deliveryInfo = $this->setDeliverInfo($data);
                break;
            } else {
                if(!$checkPrice) {
                    $checkPrice = $deliveryPrice;
                    $deliveryInfo = $this->setDeliverInfo($data);

                }
                if($duseType == 'max') { // 최대배송비 산정
                    if($deliveryPrice > $checkPrice ) {
                        $checkPrice = $deliveryPrice;
                        $deliveryInfo = $this->setDeliverInfo($data);
                    }
                } else { // 최소 배송비 산정
                    if($deliveryPrice < $checkPrice ) {
                        $checkPrice = $deliveryPrice;
                        $deliveryInfo = $this->setDeliverInfo($data);

                   }
                }
            }

        }

        $deliveryInfo['deliveryPrice'] = ($checkPrice)?$checkPrice:0;
        return $deliveryInfo;
    }
    private function checkReservePointByOption($data,$optionTotalPrice) {
        $reservePoint = 0;
        if($data->pointType == 'yes') { // 적립금 지급여부
            if($data->pointSet == 'rate') { // 정률일때
                $reservePoint = floor($optionTotalPrice * ($data->point/100));
            } else {
                $reservePoint = ($data->point * $data->camt);
            }
        }
        return $reservePoint;
    }
    private function checkReservePoint($data) {
        $reservePoint = 0;
        if($data->pointType == 'yes') { // 적립금 지급여부
            if($data->pointSet == 'rate') { // 정률일때
                $reservePoint = floor($data->sellprice * ($data->point/100));
            } else {
                $reservePoint = ($data->point * $data->camt);
            }
        }
        return $reservePoint;
    }
    private function setDeliverInfo($data) {

        return ['deliveryId'=>$data->deliveryId,
                        'fprice'=>$data->fprice,
                        'oprice'=>$data->oprice,
                        'mprice'=>$data->mprice,
                        'dpriceType'=>$data->dpriceType,
                        'localId'=>$data->localId,
                        'localUse'=>$data->localUse
               ];
    }
}
