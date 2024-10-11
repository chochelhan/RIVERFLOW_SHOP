<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\OrderProductRepositoryInterface;
use App\Models\Customize\CustomizeOrderProduct;

class BaseOrderProductRepository implements OrderProductRepositoryInterface {

    protected $orderProduct;
    public $useFields;

    public function __construct(CustomizeOrderProduct $orderProduct) {
        $this->orderProduct = $orderProduct;
        $this->useFields = $this->orderProduct->useFields;
    }

    public function getOrderProductInfo(int $id) {
        return $this->orderProduct::find($id);
    }

}

