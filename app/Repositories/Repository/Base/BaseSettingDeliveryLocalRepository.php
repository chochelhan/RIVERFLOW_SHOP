<?php
namespace App\Repositories\Repository\Base;


use App\Models\Customize\CustomizeSettingDeliveryLocal;
use App\Repositories\Interface\SettingDeliveryLocalRepositoryInterface;

class BaseSettingDeliveryLocalRepository implements SettingDeliveryLocalRepositoryInterface {

    protected $deliveryLocal;
    public $useFields;

    public function __construct(CustomizeSettingDeliveryLocal $deliveryLocal) {
        $this->deliveryLocal = $deliveryLocal;
        $this->useFields  = $this->deliveryLocal->useFields;
    }
    // 배송지 정보
    public function getDeliveryLocalInfo(int $id) {
        return $this->deliveryLocal::find($id);
    }




}

