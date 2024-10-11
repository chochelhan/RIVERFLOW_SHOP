<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeInventoryService;


/**
* 재고
*
**/
class CoreInventoryController extends Controller
{
    protected $inventoryService;


    public function __construct(CustomizeInventoryService $inventoryService) {
        $this->inventoryService = $inventoryService;

    }


    /**
    * 재고 변경
    **/
    public function updateInventoryProduct(Request $request) {
        $data = $this->inventoryService ->updateInventoryProduct($request);
        return restResponse($data);
    }

}
