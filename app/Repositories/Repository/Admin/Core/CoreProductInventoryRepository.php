<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Models\Customize\CustomizeProductInventory;
use App\Models\Customize\CustomizeInventoryHistory;

class CoreProductInventoryRepository  {

    protected $productInventory;
    protected $inventoryHistory;
    protected $useFields;

    public function __construct(CustomizeProductInventory $productInventory,CustomizeInventoryHistory $inventoryHistory) {

        $this->productInventory = $productInventory;
        $this->inventoryHistory = $inventoryHistory;
        $this->useFields  = $this->productInventory->useFields;

    }

    public function insertProductInventory(array $params) {

         $insData = $this->productInventory::create($params);
         return $insData;
    }

    public function getInventoryListByPid(int $pid) {
        return $this->productInventory::where('pid',$pid)->get();

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

    public function updateProductInventoryByPid(array $inventoryParams,int $pid) {
        return $this->productInventory::where('pid',$pid)->whereNull('oid')->update($inventoryParams);

    }

    public function getInventoryHistoryList(int $ivtId) {
           return $this->inventoryHistory::where('ivt_id',$ivtId)->orderBy('id','DESC')->get();

    }

    public function updateInventoryProduct(array $params,$id) {

           return $this->productInventory::find($id)->update($params);
    }
    //inventorySingleUpdate

}

