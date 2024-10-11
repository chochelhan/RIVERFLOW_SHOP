<?php

namespace App\Repositories\Repository\Base;
use App\Models\Customize\CustomizeProductInquire;


class BaseProductInquireRepository  {

    protected $productInquire;
    public $useFields;

    public function __construct(CustomizeProductInquire $productInquire) {
        $this->productInquire = $productInquire;
        $this->useFields = $this->productInquire->useFields;
    }



}

