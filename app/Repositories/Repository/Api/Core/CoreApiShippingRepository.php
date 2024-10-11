<?php

namespace App\Repositories\Repository\Api\Core;

use App\Models\Customize\CustomizeShipping;


class CoreApiShippingRepository {

    protected $shipping;

    public function __construct(CustomizeShipping $shipping) {
        $this->shipping = $shipping;
        $this->useFields  = $this->shipping->useFields;
    }


    //
    public function getShippingInfo(int $id) {
        return $this->shipping::find($id);
    }

    public function getMyShippingInfo(int $user_id,int $id) {
        return $this->shipping::where('user_id',$user_id)->where('id',$id)->first();
    }

    // 배송지 목록
    public function getShippingList(int $user_id) {
        return $this->shipping::orderBy('defaultShipping','desc')->where('user_id',$user_id)->get();
    }
    // 기본배송지 존재여부
    public function getDefaultShippingInfo(int $user_id) {
        return $this->shipping::where('user_id',$user_id)->where('defaultShipping','yes')->first();
    }
    // 저장
    public function insertShipping(array $fieldset) {
        if(empty($fieldset['user_id']))return false;
        if($fieldset['defaultShipping'] == 'yes') {
            $info = $this->getDefaultShippingInfo($fieldset['user_id']);
            if($info) {
                $this->shipping::find($info->id)->update(['defaultShipping'=>'no']);
            }
        }
        return $this->shipping::create($fieldset);
    }
    // 수정
    public function updateShipping(int $id,$fieldset) {
        if(empty($fieldset['user_id']))return false;
        if($fieldset['defaultShipping'] == 'yes') {
            $info = $this->getDefaultShippingInfo($fieldset['user_id']);
            if($info) {
                $this->shipping::find($info->id)->update(['defaultShipping'=>'no']);
            }
        }
        return $this->shipping::find($id)->update($fieldset);
    }
    // 기본배송지 변경
    public function updateDefaultShipping(int $user_id,int $id) {
        $info = $this->getDefaultShippingInfo($user_id);
        if($info) {
            $this->shipping::find($info->id)->update(['defaultShipping'=>'no']);
        }
        return $this->shipping::find($id)->update(['defaultShipping'=>'yes']);
    }
    // 삭제
    public function deleteShipping(int $user_id,int $id) {
        $info = $this->getShippingInfo($id);
        $defaultShipping = $info->defaultShipping;
        if($defaultShipping=='yes') { // 삭제할 정보에 기본 배송지 정보가 있으면
            $del = $this->shipping::destroy($id);
            $userShipiingInfo = $this->shipping::where('user_id',$user_id)->orderBy('id','desc')->first();
            if($userShipiingInfo) {
                return $this->shipping::find($userShipiingInfo->id)->update(['defaultShipping'=>'yes']);
            } else return $del;
        } else {
            return $this->shipping::destroy($id);
        }

    }


}

