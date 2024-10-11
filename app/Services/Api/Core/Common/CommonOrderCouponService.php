<?php

namespace App\Services\Api\Core\Common;

class CommonOrderCouponService {


    private $targetProudctList;
    private $denyProductList;
    public $productCouponPid;
    public $totalPrice; // 쿠폰이 적용가능한 총 구매금액

    /// 사용가능한 쿠폰 데이타 가져오기
    public function getUserCouponCheck($cpndata,$cartData) {
        $productTarget = false;
        switch($cpndata->ptType) {
            case 'all': //(전체)
                $productTarget = true;
                if($cpndata->ptDeny == 'yes') { // 할인 제외대상 상품
                    $flag = $this->checkOutProduct($cpndata->ptOutData,$cartData['resultPids']);
                    if(!$flag) {
                        $productTarget = false;
                       return false;
                    }
                } else {
                    foreach($cartData['resultPids'] as $pid=>$pidData) {
                        $this->targetProudctList[$pid] = $pidData['priceInfo'];
                    }
                }
                break;
            case 'single': // (개별지정) ,
                $ptInData = json_decode($cpndata->ptInData); // 대상 상품
                $flag = false;
                foreach($ptInData as $pid) {
                   if(!empty($cartData['resultPids'][$pid])) { // 구매 상품중 개별지정 상품에서 대상에 하나라도 존재하면
                        $flag = true;
                        $this->targetProudctList[$pid] = $cartData['resultPids'][$pid]['priceInfo'];
                   }
                }
                if(!$flag) {
                   return false;
                } else {
                    $productTarget = true;
                }
                break;
            case 'brand': // (브랜드)
                $ptInData = json_decode($cpndata->ptInData); // 대상 브랜드
                $usePidList = [];
                foreach($ptInData as $brand) {
                    foreach($cartData['resultPids'] as $pid=>$pidData) { // 구매상품의 브랜드 중에서 할인대상브랜드가 존재하는 상품정보를 usePidList 에 넣은다
                        if($pidData['brandId'] == $brand->value) {
                           $usePidList[$pid] = $pidData;
                        }
                    }
                }
                if(count($usePidList)>0) {
                    $productTarget = true;
                    if($cpndata->ptDeny == 'yes') { // 할인 제외대상 상품
                        $flag = $this->checkOutProduct($cpndata->ptOutData,$usePidList);
                        if(!$flag) {
                            $productTarget = false;
                            return false;
                        }
                    } else {
                         foreach($usePidList as $pid=>$pidData) {
                            $this->targetProudctList[$pid] = $pidData['priceInfo'];
                         }
                    }
                }
                break;
            case 'category': // (카테고리)
                $usePidList = [];
                $ptInData = json_decode($cpndata->ptInData); // 대상 카테고리
                foreach($ptInData as $categorys) {
                    foreach($cartData['resultPids'] as $pid=>$pidData) { // 구매상품의 브랜드 중에서 할인대상브랜드가 존재하는 상품정보를 usePidList 에 넣은다
                        $flag = false;
                        foreach($pidData['category'] as $cateData) {
                            if(count($cateData)>0) {
                                $checkFlag = $this->checkInCategorys($cateData,$categorys);
                                if($checkFlag)$flag= true;
                            }
                        }
                        if($flag)$usePidList[$pid] = $pidData;
                    }
                }
                if(count($usePidList)>0) {
                    $productTarget = true;
                    if($cpndata->ptDeny == 'yes') { // 할인 제외대상 상품
                        $flag = $this->checkOutProduct($cpndata->ptOutData,$usePidList);
                        if(!$flag) {
                            $productTarget = false;
                            return false;
                        }
                    } else {
                        foreach($usePidList as $pid=>$pidData) {
                            $this->targetProudctList[$pid] = $pidData['priceInfo'];
                        }
                    }
                }
                break;
        }
        if($productTarget) {
            if($cpndata->ctype == 'basket') { // 장바구니 쿠폰일때 (장바구니에 담긴 상품중 제외대상을 제외한 모든 상품이 할인대상)
                if($this->denyProductList && count($this->denyProductList)>0) {
                    $minusPrice = 0;
                    foreach($this->denyProductList as $price) {
                        $minusPrice+=$price;
                    }
                    $checkTotalPrice = $cartData['totalPrice'] - $minusPrice;
                } else {
                    $checkTotalPrice = $cartData['totalPrice'];
                }
            } else { // 상품쿠폰 (해당상품 1개만 할인됨)
                $checkTotalPrice = 0;
                foreach($this->targetProudctList as $pid=>$price) {
                    if($checkTotalPrice < $price) {
                        $this->productCouponPid = $pid;
                        $checkTotalPrice = $price;
                    }
                 }
            }
            if($cpndata->minPriceUse == 'yes' && $cpndata->minPrice > $checkTotalPrice) { // 최소금액 체크
                return false;
            }
            if($cpndata->maxPriceUse == 'yes' && $cpndata->maxPrice < $checkTotalPrice) { // 최대금액 체크
                return false;
            }
            $this->totalPrice = $checkTotalPrice;
            return $cpndata;
        }
        return false;
    }


    // 쿠폰 할인제외 대상에 포함되지 않는 상품 존재여부 (true(사용가능) ,false(불가))
    private function checkOutProduct($ptOutData,$resultPids) {
        if(empty($ptOutData)) return true;
        if(empty($resultPids))return false;

        // 할인제외 상품
        foreach(json_decode($ptOutData) as $pid)$outPidList[$pid] = $pid;

        $flag = false;
        $targetProudctList = [];
        $denyProductList = [];
        foreach($resultPids as $pid=>$pidData) {

            if(empty($outPidList[$pid])) { // 구매상품중 제외대상 상품이 존재하지 않으면
                $flag = true;
                $targetProudctList[$pid] = $pidData['priceInfo'];
            } else {
                $denyProductList[] = $pidData['priceInfo'];
            }
        }
        $this->targetProudctList = $targetProudctList;
        $this->denyProductList = $resultPids;

        return $flag;
    }
    // 쿠폰 할인제외 대상에 포함되는 카테고리 존재여부 (true(사용가능) ,false(불가))
    private function checkInCategorys($cateData,$categorys) {
        $flag = false;
        switch(count($cateData)) {
            case "1": // 상품카테고리가 1차만 있는것
                if($categorys->cate1 == $cateData[0])$flag = true;
            break;
            case "2": // 상품카테고리가 2차만 있는것
                 if(!empty($categorys->cate2)) {
                    if($categorys->cate2 == $cateData[1])$flag = true;
                 } else {
                    if($categorys->cate1 == $cateData[0])$flag = true;
                 }
            break;
            case "3": // 상품카테고리가 3차만 있는것
                 if(!empty($categorys->cate3)) {
                    if($categorys->cate3 == $cateData[2] )$flag = true;
                 } else if(!empty($categorys->cate2)) {
                    if($categorys->cate2 == $cateData[1] )$flag = true;
                 } else {
                    if($categorys->cate1 == $cateData[0] )$flag = true;
                 }
            break;
        }
        return $flag;
    }


    public function getUseCouponPrice($couponInfo,$cartData) {
        $totalPrice = $cartData['totalPrice'];
        $data = $this->getUserCouponCheck($couponInfo,$cartData);

        $couponDiscountPrice = 0;
        $discountPrice = 0;
        if($data) {
            $targetOrderPrice = $this->totalPrice; // 할인가능한 상품의 총 구매금액
            $remainTotalPrice = $totalPrice - $targetOrderPrice;
            if($couponInfo->discountType == 'fix') {
                $discountPrice = $couponInfo->discountPrice;
                $couponDiscountPrice = $targetOrderPrice - $discountPrice;
            } else {
                $discountPrice = floor(floor($targetOrderPrice * ($couponInfo->discountRate/100))/100) * 100; // 100 단위 절삭
                if(!empty($couponInfo->discountRatePrice) && $couponInfo->discountRatePrice>0) {
                    if($discountPrice > $couponInfo->discountRatePrice) { //최대 금액을 넘지 않도록
                        $discountPrice = $couponInfo->discountRatePrice;
                    }
                }
                $couponDiscountPrice = $targetOrderPrice - $discountPrice;
            }
            $result['useCouponPrice'] = $discountPrice;
            $result['totalPrice'] = $remainTotalPrice + $couponDiscountPrice;
            $result['pointDeny'] = $couponInfo->pointDeny;

            return $result;
        } else return false;
    }
}
