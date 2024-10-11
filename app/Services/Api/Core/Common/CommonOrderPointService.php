<?php

namespace App\Services\Api\Core\Common;

class CommonOrderPointService {


    /**
    *  포인트 사용시
    * @params $usePoint(사용포인트),$havePoint(보유포인트),$setting(포인트 설정정보),$totalPrice((상품/쿠폰)할인된최종금액), $totalMaxUsePoint(사용가능 최대적립금)
    *
    *@ output error {wrongUsePoint,wrongSetting,minPoint,maxPoint}
    *
    **/
    public function getUserPointPrice(int $usePoint,int $havePoint,$setting,int $totalPrice,int $totalMaxUsePoint) {

        if($usePoint > $havePoint || !$havePoint) {
            return ['result'=>'wrongUsePoint'];
        }
        if($setting->usePointMinPriceUse == 'yes') { //  적립금 최소 결제금액 사용시
            if ($totalPrice < $setting->usePointMinPrice) {
                return ['result'=>'wrongSetting']; // 최소결제 부족
            }
        }
        if ($setting->usePointMinUse == 'yes') { //  최소 사용가능 적립금
            if ($usePoint < $setting->usePointMin) {
                return ['result'=>'minPoint']; //
            }
        }
        if($setting->usePointMaxUse == 'yes') { //  최대 사용가능 적립금
            $maxPoint = $totalPrice * ($setting->usePointMax/100);
            $maxPoint = floor($maxPoint);
            if ($usePoint > $maxPoint) {
                 return ['result'=>'maxPoint']; //
            }
        }

        if ($usePoint > $totalMaxUsePoint) {
            $usePoint = $totalMaxUsePoint;
        }
        return ['result'=>'success','point'=>$usePoint]; //
    }
}
