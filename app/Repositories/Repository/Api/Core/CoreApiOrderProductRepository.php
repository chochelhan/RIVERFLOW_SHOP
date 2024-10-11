<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseOrderProductRepository;


class CoreApiOrderProductRepository extends  BaseOrderProductRepository {

    public $isLoginInfo;

    public function insertOrderProduct(array $fieldset) {
        $this->orderProduct::create($fieldset);

    }

}

