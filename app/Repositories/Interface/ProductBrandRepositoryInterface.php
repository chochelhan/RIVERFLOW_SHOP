<?php

namespace App\Repositories\Interface;

interface ProductBrandRepositoryInterface {

    public function getBrandUseList();
    public function getBrandInfo(int $id);

}
