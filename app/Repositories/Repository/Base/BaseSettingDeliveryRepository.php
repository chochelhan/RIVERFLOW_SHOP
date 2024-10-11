<?php
namespace App\Repositories\Repository\Base;


use App\Models\Customize\CustomizeSettingDelivery;
use App\Repositories\Interface\SettingDeliveryRepositoryInterface;

class BaseSettingDeliveryRepository implements SettingDeliveryRepositoryInterface {

    protected $delivery;
    public $useFields;

    public function __construct(CustomizeSettingDelivery $delivery) {
        $this->delivery = $delivery;
        $this->useFields  = $this->delivery->useFields;
    }
    // 배송지 정보
    public function getDeliveryInfo(int $id) {
        return $this->delivery::find($id);
    }





}

