<?php
namespace App\Repositories\Repository\Base;


use App\Models\Customize\CustomizeCoupon;
use App\Models\Customize\CustomizeCouponPublish;
use App\Models\Customize\CustomizeMember;

use App\Repositories\Interface\CouponRepositoryInterface;

class BaseCouponRepository implements CouponRepositoryInterface {

    protected $coupon;
    protected $couponPublish;
    protected $member;

    public $useFields;
    public $publishUseFields;
    public $GTYPES;

    public function __construct(CustomizeCoupon $coupon,CustomizeCouponPublish $couponPublish,CustomizeMember $member) {
        $this->coupon = $coupon;
        $this->couponPublish = $couponPublish;
        $this->member = $member;

        $this->useFields  = $this->coupon->useFields;
        $this->publishUseFields  = $this->couponPublish->useFields;
        $this->GTYPES = $this->couponPublish->GTYPES;
    }
    // 쿠폰 정보
    public function getCouponInfo(int $id) {
        return $this->coupon::find($id);
    }




}

