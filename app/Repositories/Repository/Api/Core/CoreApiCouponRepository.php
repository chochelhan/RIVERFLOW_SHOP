<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseCouponRepository;


class CoreApiCouponRepository extends  BaseCouponRepository {


    //회원이 보유한 쿠폰 list 가져오기
    public function getUserCouponList(int $userId) {
        $nowDate = date('Y-m-d');
        $cpnTable = $this->coupon->table;
        $pubTable = $this->couponPublish->table;
        $select = [$pubTable.'.*',
                   $cpnTable.'.minPriceUse', // 사용시 최소금액 사용여부
                  $cpnTable.'.minPrice',
                  $cpnTable.'.maxPriceUse', // 사용시 최대 금액 사용여부
                  $cpnTable.'.maxPrice',
                  $cpnTable.'.pointDeny', // 포인트 동시 사용여부
                  $cpnTable.'.ptType', // 해당되는 상품 all (전체) , single (개별지정) , brand (브랜드) ,category (카테고리)
                  $cpnTable.'.ptInData', //대상 상품
                  $cpnTable.'.ptOutData', // 제외대상 상품
                  $cpnTable.'.ptDeny', // 할인 제외대상 사용여부 yes or no
                  $cpnTable.'.cplatform'];

        $data = $this->couponPublish::orderBy($pubTable.'.id','desc')
                    ->leftJoin($cpnTable,$cpnTable.'.id','=',$pubTable.'.cid')
                    ->select($select)
                    ->where($pubTable.'.user_id',$userId)
                    ->where($pubTable.'.cuse','no')
                    ->where($pubTable.'.expireStdate','<=',$nowDate.' 00:00:00') // 사용기간 시작
                    ->where($pubTable.'.expireEndate','>=',$nowDate.' 23:59:59') // 사용기간 종료일
                    ->get();

        return $data;
    }

    //회원이 보유한 쿠폰 all list 가져오기
    public function getUserCouponAllList(int $userId) {
        $nowDate = date('Y-m-d');
        $cpnTable = $this->coupon->table;
        $pubTable = $this->couponPublish->table;
        $select = [$pubTable.'.*',
                   $cpnTable.'.minPriceUse', // 사용시 최소금액 사용여부
                  $cpnTable.'.minPrice',
                  $cpnTable.'.maxPriceUse', // 사용시 최대 금액 사용여부
                  $cpnTable.'.maxPrice',
                  $cpnTable.'.pointDeny', // 포인트 동시 사용여부
                  $cpnTable.'.ptType', // 해당되는 상품 all (전체) , single (개별지정) , brand (브랜드) ,category (카테고리)
                  $cpnTable.'.ptInData', //대상 상품
                  $cpnTable.'.ptOutData', // 제외대상 상품
                  $cpnTable.'.ptDeny', // 할인 제외대상 사용여부 yes or no
                  $cpnTable.'.cplatform'];

        $data = $this->couponPublish::orderBy($pubTable.'.id','desc')
                    ->leftJoin($cpnTable,$cpnTable.'.id','=',$pubTable.'.cid')
                    ->select($select)
                    ->where($pubTable.'.user_id',$userId)
                    ->get();

        return $data;
    }

    //회원이 보유한 쿠폰 row 가져오기
    public function getUserCouponInfo(int $id,int $userId) {
        $nowDate = date('Y-m-d');
        $cpnTable = $this->coupon->table;
        $pubTable = $this->couponPublish->table;
        $select = [$pubTable.'.*',
                   $cpnTable.'.minPriceUse', // 사용시 최소금액 사용여부
                  $cpnTable.'.minPrice',
                  $cpnTable.'.maxPriceUse', // 사용시 최대 금액 사용여부
                  $cpnTable.'.maxPrice',
                  $cpnTable.'.pointDeny', // 포인트 동시 사용여부
                  $cpnTable.'.ptType', // 해당되는 상품 all (전체) , single (개별지정) , brand (브랜드) ,category (카테고리)
                  $cpnTable.'.ptInData', //대상 상품
                  $cpnTable.'.ptOutData', // 제외대상 상품
                  $cpnTable.'.ptDeny', // 할인 제외대상 사용여부 yes or no
                  $cpnTable.'.cplatform'];

        $data = $this->couponPublish::where($pubTable.'.id',$id)
                    ->leftJoin($cpnTable,$cpnTable.'.id','=',$pubTable.'.cid')
                    ->select($select)
                    ->where($pubTable.'.user_id',$userId)
                    ->where($pubTable.'.cuse','no')
                    ->where($pubTable.'.expireStdate','<=',$nowDate.' 00:00:00') // 사용기간 시작
                    ->where($pubTable.'.expireEndate','>=',$nowDate.' 23:59:59') // 사용기간 종료일
                    ->first();

        return $data;
    }
    // 주문정보에서  쿠폰 row 가져오기
    public function getCouponInfoByOrder(int $id,int $userId) {
        $nowDate = date('Y-m-d');
        $cpnTable = $this->coupon->table;
        $pubTable = $this->couponPublish->table;
        $select = [$pubTable.'.*',
                  $cpnTable.'.minPriceUse', // 사용시 최소금액 사용여부
                  $cpnTable.'.minPrice',
                  $cpnTable.'.maxPriceUse', // 사용시 최대 금액 사용여부
                  $cpnTable.'.maxPrice',
                  $cpnTable.'.pointDeny', // 포인트 동시 사용여부
                  $cpnTable.'.ptType', // 해당되는 상품 all (전체) , single (개별지정) , brand (브랜드) ,category (카테고리)
                  $cpnTable.'.ptInData', //대상 상품
                  $cpnTable.'.ptOutData', // 제외대상 상품
                  $cpnTable.'.ptDeny', // 할인 제외대상 사용여부 yes or no
                  $cpnTable.'.cplatform'];

        $data = $this->couponPublish::where($pubTable.'.id',$id)
                    ->leftJoin($cpnTable,$cpnTable.'.id','=',$pubTable.'.cid')
                    ->select($select)
                    ->where($pubTable.'.user_id',$userId)
                    ->first();

        return $data;
    }

    //회원이 보유한 쿠폰수
    public function getUserCouponCount(int $userId) {
        $nowDate = date('Y-m-d');
        return $this->couponPublish::where('user_id',$userId)->where('cuse','no')
                                   ->where('expireStdate','<=',$nowDate.' 00:00:00') // 사용기간 시작
                                   ->where('expireEndate','>=',$nowDate.' 23:59:59') // 사용기간 종료일
                                   ->count();

    }
    public function useCoupon(int $pubId,string $use) {
        return $this->couponPublish::where('id',$pubId)->update(['cuse'=>$use]);
    }
}

