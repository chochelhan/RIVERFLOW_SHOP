<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseOrderRepository;


class CoreApiOrderRepository extends  BaseOrderRepository {

    public $isLoginInfo;

    public function insertOrder(array $fieldset) {
        $insData = $this->order::create($fieldset);
        $insData->fresh();
        return $insData;

    }
    public function updateOrder(int $id,array $fieldset) {
        $this->order::find($id)->update($fieldset);
    }
    public function getOrderInfoByUser(int $id,String $user_code) {
        return $this->order::where('id',$id)->where('user_code',$user_code)->first();
    }

    public function getMyOrderStatus(int $user_code,array $params) {


        $list = $this->orderProduct::select('ostatus','oid')
                ->where('user_id',$user_code)
                ->when($params,function($query,$params) {
                     if(!empty($params['stdate']) && !empty($params['endate'])) {
                         $query->where('created_at','>=',$params['stdate'].' 00:00:00');
                         $query->where('created_at','<=', $params['endate'].' 23:59:59');
                     }
                })
                ->orderBy('ostatus')->orderBy('oid')->get();
        $statusList = [];
        $resultList = [];
        foreach($list as $data) {
            if(empty($statusList[$data->ostatus])) {
                $statusList[$data->ostatus] = [];
            }
            if(empty($statusList[$data->ostatus][$data->oid])) {
                $statusList[$data->ostatus][$data->oid] = 1;
                $resultList[$data->ostatus] = count($statusList[$data->ostatus]);
            }

        }
        return $resultList;
    }

    public function getMyOrderList(int $user_code,array $params) {
        $this->order->setQueryParams($params);
        $oids = '';
        if(!empty($params['ostatus'])) {
            $ostatus = (!empty($params['ostatus']))?$params['ostatus']:'';
            $productList = $this->orderProduct::select('oid')
                ->distinct()
                ->where('user_id',$user_code)
                ->when($ostatus,function($query,$ostatus) {
                    $pstatuses = explode(',',$ostatus);
                    $queryData = [];
                    $vals = [];
                    foreach($pstatuses as $val) {
                        switch($val) {
                            case 'CZ'://=> '주문취소',
                                foreach($this->cancleList as $key=>$text) {
                                    $queryData[] = 'ostatus = ?';
                                    $vals[] = $key;
                                }
                                break;
                            case 'RZ': //=> '반품',
                                foreach($this->returnList as $key=>$text) {
                                    $queryData[] = 'ostatus = ?';
                                    $vals[] = $key;
                                }
                                break;
                            case 'EZ': //=> '교환',
                                foreach($this->exchangeList as $key=>$text) {
                                    $queryData[] = 'ostatus = ?';
                                    $vals[] = $key;
                                }
                                break;
                            case 'FZ': //=> '환불',
                                foreach($this->refundList as $key=>$text) {
                                    $queryData[] = 'ostatus = ?';
                                    $vals[] = $key;
                                }
                                break;
                            default:
                                $queryData[] = $this->opTable.'.ostatus = ?';
                                $vals[] = $val;
                                break;
                        }
                    }
                    $queryString = '('.implode(' OR ',$queryData).')';
                    $query->whereRaw($queryString,$vals);
                })->get();
                $oids = [];
                foreach($productList as $val) {
                    $oids[] = $val->oid;
                }
                if(count($oids)<1) {
                    $oids = ['not'];
                }

        }
        $list = $this->order::orderBy('id','desc')
                     ->where('user_code',$user_code)
                     ->when($oids,function($query,$oids) {
                         if($oids[0]=='not') {
                            $query->where('id','<',1);
                         } else {
                            $query->whereIn('id',$oids);
                         }
                     })
                     ->where(function($query) {
                         $queryParams = $this->order->queryParams;
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where('created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where('created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }

                         if(!empty($queryParams['paymethod'])) {
                            $pstatuses = explode(',',$queryParams['paymethod']);
                            $queryData = [];
                            $vals = [];
                            foreach($pstatuses as $val) {
                                $queryData[] = 'paymethod = ?';
                                $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }


                     })
                    ->paginate($params['limit']);

        $reviewTable = config('tables.orderReview');
        $opTable = $this->orderProduct->table;
        $opSelect= [$opTable.'.*',$reviewTable.'.id as isReview'];
        foreach($list as $key=>$val) {
            $orderProducts = $this->orderProduct::where($opTable.'.oid',$val->id)
                                   ->select($opSelect)
                                    ->leftJoin($reviewTable,function($join) {
                                        $reviewTable = config('tables.orderReview');
                                        $opTable = $this->orderProduct->table;
                                        $join->on($reviewTable.'.oid','=',$opTable.'.oid')->on($reviewTable.'.pid','=',$opTable.'.pid');
                                    })
                                    ->get();
            $val['orderProducts'] = $orderProducts;
            $list[$key] = $val;
        }

        return $list;
    }



    public function getMyAbleReviewOrderList(int $user_id,array $params) {

        $this->orderProduct->setQueryParams($params);
        $list = $this->orderProduct::orderBy('id','desc')
                     ->where('user_id',$user_id)
                     ->where('ostatus','OC')
                     ->where(function($query) {
                         $queryParams = $this->orderProduct->queryParams;
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where('created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where('created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }

                     })
                     ->get();
        return $list;
    }
    public function getMyOrderListWithProduct(int $user_code,array $params) {
        $this->order->setQueryParams($params);
        $select = [$this->table.'.*',
            $this->opTable.'.pname',
            $this->opTable.'.listImg',
            $this->opTable.'.opt_name'];

        $list = $this->order::orderBy($this->table.'.id','desc')
                             ->select($select)
                             ->leftJoin($this->opTable,$this->table.'.id','=',$this->opTable.'.oid')
                             ->where($this->table.'.user_code',$user_code)
                             ->where(function($query) {
                                 $queryParams = $this->order->queryParams;
                                 if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                                    if(!empty($queryParams['stdate'])) {
                                       $query->where($this->table.'.created_at','>=',$queryParams['stdate'].' 00:00:00');
                                    }
                                    if(!empty($queryParams['endate'])) {
                                       $query->where($this->table.'.created_at','<=', $queryParams['endate'].' 23:59:59');
                                    }
                                 }
                                 if(!empty($queryParams['ostatus'])) {
                                    $pstatuses = explode(',',$queryParams['ostatus']);
                                    $queryData = [];
                                    $vals = [];
                                    foreach($pstatuses as $val) {
                                        $queryData[] = $this->opTable.'.ostatus = ?';
                                        $vals[] = $val;
                                    }
                                    $queryString = '('.implode(' OR ',$queryData).')';
                                    $query->whereRaw($queryString,$vals);
                                 }
                                 if(!empty($queryParams['paymethod'])) {
                                    $pstatuses = explode(',',$queryParams['paymethod']);
                                    $queryData = [];
                                    $vals = [];
                                    foreach($pstatuses as $val) {
                                        $queryData[] = $this->table.'.paymethod = ?';
                                        $vals[] = $val;
                                    }
                                    $queryString = '('.implode(' OR ',$queryData).')';
                                    $query->whereRaw($queryString,$vals);
                                 }


                             })
                            ->paginate($params['limit']);


         return $list;
    }

    // 주문 상품 상세
    public function getMyOrderDetail(int $user_code,int $id) {
        $select = [
            $this->table.'.*',
            $this->opTable.'.id as opId',
            $this->opTable.'.listImg',
            $this->opTable.'.pname',
            $this->opTable.'.opt_name',
            $this->opTable.'.pid',
            $this->opTable.'.dcprice',
            $this->opTable.'.optionSingleInfos',
            $this->opTable.'.couponId',
            $this->opTable.'.serviceType',
            $this->opTable.'.payprice', // 최종구매금액
            $this->opTable.'.oamt as ordAmt', //구매수량
            $this->opTable.'.ostatus as ordStatus',
            
        ];
        return $this->order::select($select)
                           ->where($this->table.'.id',$id)
                           ->leftJoin($this->opTable,$this->table.'.id','=',$this->opTable.'.oid')
                           ->where($this->table.'.user_code',$user_code)
                           ->get();
    }

    // 주문 상품 목록
    public function getMyOrderProductList(int $user_code,int $oid) {
        return $this->orderProduct::orderBy($this->opTable.'.id','desc')
                                    ->select($this->opTable.'.*')
                                    ->leftJoin($this->table,$this->table.'.id','=',$this->opTable.'.oid')
                                    ->where($this->table.'.id',$oid)
                                    ->where($this->table.'.user_code',$user_code)
                                    ->get();
    }
    // 주문 상품 목록(id 값으로 가져오기)
    public function getMyOrderProductListByIds(string $user_code,array $ids) {

        $select = [
            $this->opTable.'.*',
            $this->table.'.paymethod',
            $this->table.'.useCouponId',
            $this->table.'.useCouponPrice',
            $this->table.'.usePoint',
            $this->table.'.price as totalPrice'
            ];

        return $this->orderProduct::orderBy($this->opTable.'.id','desc')
                                    ->select($select)
                                    ->leftJoin($this->table,$this->table.'.id','=',$this->opTable.'.oid')
                                    ->whereIn($this->opTable.'.id',$ids)
                                    ->where($this->table.'.user_code',$user_code)
                                    ->get();
    }
    // 주문 상품 목록($oid 로 가져오기)
    public function getOrderProductListByOid(int $oid) {
        return $this->orderProduct::where('oid',$oid)->get();
    }

    // 주문 상품 목록($oid,$pid 로 가져오기)
    public function getOrderProductListByOidPid(int $oid,int $pid) {
        return $this->orderProduct::where('oid',$oid)->where('pid',$pid)->get();
    }

    //////  클레임 주문상태 변경
    public function updateMyOrderClaimStatus(array $ids,string $changeStatus) {
        $date = date('Y-m-d H:i:s');
        return $this->orderProduct::whereIn('id',$ids)->update(['ostatus'=>$changeStatus,'claimDate'=>$date]);
    }
    ////// 주문상태 변경
    public function updateMyOrderStatus(array $ids,string $changeStatus) {
        return $this->orderProduct::whereIn('id',$ids)->update(['ostatus'=>$changeStatus]);
    }

    public function updateOrderProductOstatusByOid(int $oid,string $changeStatus) {
        return $this->orderProduct::where('oid',$oid)->update(['ostatus'=>$changeStatus]);
    }

}

