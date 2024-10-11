<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiCouponRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;


class CoreApiMyCouponService extends CoreApiAuthHeader {

    protected $couponRepository;


    public function __construct(Request $request,CustomizeApiCouponRepository $couponRepository) {

        parent::__construct($request);

        $this->couponRepository = $couponRepository;

    }

    public function getMyCouponList() {
        if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
        }

        $data = $this->couponRepository->getUserCouponAllList($this->isLoginInfo->id);
        return ['status'=>'success','data'=>$data];
    }

    public function insertMyCoupon(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        //$requestParams = $request->all();
        //return ['status'=>$status,'data'=>$data];
    }


}