<?php

namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizeOrderClaim;
use App\Models\Customize\CustomizeOrderClaimProduct;

class BaseOrderClaimRepository  {

    protected $orderClaim;
    public $useFields;

    protected $orderClaimProduct;
    public $productUseFields;

    public function __construct(CustomizeOrderClaim $orderClaim,CustomizeOrderClaimProduct $orderClaimProduct) {
        $this->orderClaim = $orderClaim;
        $this->useFields = $this->orderClaim->useFields;

        $this->orderClaimProduct = $orderClaimProduct;
        $this->productUseFields = $this->orderClaimProduct->useFields;

    }



}

