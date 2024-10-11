<?php

namespace App\Repositories\Interface;

interface ProductCategoryRepositoryInterface {


    public function getCategoryUseList();
    public function getCategoryInfo(int $id);


}
