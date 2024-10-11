<?php

namespace App\Repositories\Repository\Api\Core;
use App\Models\Customize\CustomizeProductInventory;
use App\Models\Customize\CustomizeInventoryHistory;

class CoreApiProductInventoryRepository  {

    protected $productInventory;
    protected $inventoryHistory;
    protected $useFields;

    public function __construct(CustomizeProductInventory $productInventory,CustomizeInventoryHistory $inventoryHistory) {

        $this->productInventory = $productInventory;
        $this->inventoryHistory = $inventoryHistory;
        $this->useFields  = $this->productInventory->useFields;

    }

    public function getInventoryInfo(int $id) {
        return $this->productInventory::find($id);

    }
    public function getInventoryInfoByPid(int $pid) {
        return $this->productInventory::where('pid',$pid)->whereNull('oid')->first();

    }
    public function getInventoryInfoByOid(int $oid) {
        return $this->productInventory::where('oid',$oid)->where('optionUse','yes')->first();

    }


    public function updateInventoryProduct(array $params,$id) {

           return $this->productInventory::find($id)->update($params);

    }
}

