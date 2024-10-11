<?php

namespace App\Services\Api\Core\Common;
use App\Repositories\Repository\Api\Customize\CustomizeApiPointRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiCouponRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;

class CommonOrderRecoverService {

    private $pointRepository;
    private $couponRepository;
    private $memberRepository;

    public function __construct(CustomizeApiPointRepository $pointRepository,
                                CustomizeApiMemberRepository $memberRepository,
                                CustomizeApiCouponRepository $couponRepository) {

        $this->pointRepository = $pointRepository;
        $this->couponRepository = $couponRepository;
        $this->memberRepository = $memberRepository;

    }
    /**
    *
    * params {
    *    oid,
    *    useCouponId,
    *    useCouponPrice,
    *    usePoint,
    *    }
    * user_id
    **/
    // 포인트 및 쿠폰 복구

    public function discountRecoverAll(array $params,int $user_id) {

        if(!empty($params['useCouponId'])) {
            $this->couponRepository->useCoupon($params['useCouponId'],'no'); // 쿠폰 복구
        }

        if(!empty($params['usePoint'])) {
           $ptParams['user_id'] = $user_id;
           $ptParams['ptype'] = 'plus';
           $ptParams['point'] = $params['usePoint'];
           $ptParams['oid'] = $params['oid'];
           $ptParams['pointMsg'] = $this->pointRepository->PCODES[$params['pcode']];
           $ptParams['pcode'] = $params['pcode'];

           $this->pointRepository->insertPoint($ptParams);
           $this->memberRepository->updateMemberPoint($ptParams);
        }

    }

}