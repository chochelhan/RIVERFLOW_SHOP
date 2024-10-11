<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\OrderRepositoryInterface;
use App\Models\Customize\CustomizeOrder;
use App\Models\Customize\CustomizeOrderProduct;
use App\Models\Customize\CustomizeOrderHistory;
use App\Models\Customize\CustomizeOrderClaim;

class BaseOrderRepository implements OrderRepositoryInterface {

    protected $order;
    protected $orderProduct;
    protected $orderHistory;
    protected $orderClaim;

    public $useFields;

    public $ostatusList = [];
    public $cancleList =  [];
    public $returnList =  [];
    public $exchangeList = [];
    public $refundList = [];
    public $paymethodList = [];
    public $opTable;
    public $table;
    public $clmTable;

    public function __construct(CustomizeOrder $order,CustomizeOrderProduct $orderProduct,
                                CustomizeOrderHistory $orderHistory,CustomizeOrderClaim $orderClaim) {

        $this->order = $order;
        $this->orderProduct = $orderProduct;
        $this->orderHistory = $orderHistory;
        $this->orderClaim = $orderClaim;

        $this->opTable = $this->orderProduct->table;
        $this->table = $this->order->table;
        $this->clmTable = $this->orderClaim->table;

        $this->useFields = $this->order->useFields;

        $this->ostatusList = config('order.status');
        $this->cancleList =  config('order.cancleStatus');
        $this->returnList =  config('order.returnStatus');
        $this->exchangeList = config('order.exchangeStatus');
        $this->refundList = config('order.refundStatus');
        $this->paymethodList = config('order.paymethods');

    }

    public function getOrderInfo(int $id) {
        return $this->order::find($id);
    }
    
    public function getOrderProductList(int $oid) {
        return $this->orderProduct::where('oid',$oid)->get();
    }

    public function getOrderProductInfo(int $id) {
        return $this->orderProduct::find($id);
    }

    public function getOrderByDelivery($company,$invcNo) {

        return $this->order::where('deliveryCompany',$company)
                                    ->where('sendNumber',$invcNo)
                                    ->get();

    }
    //////
    public function updateOrder(int $id,array $params) {
        return $this->order::where('id',$id)->update($params);
    }
}

