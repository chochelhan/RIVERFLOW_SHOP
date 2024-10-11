<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseOrderClaimRepository;

class CoreOrderClaimRepository extends BaseOrderClaimRepository {


    public function getClaimDataList(int $oid,int $opid) {

        //return $this->orderClaim::where('oid',$oid)->where('opid',$opid)->orderBy('id','desc')->first();
        return $this->orderClaim::where('oid',$oid)->orderBy('id','desc')->first();
    }

    // 클레임 상태별 총수
    public function getClaimCount(array $params) {
        
        return $this->orderClaim::whereDate($params['dateKey'],'>=',$params['dateValue'])->get();

    }

    // 클레임 주문 목록
    public function getClaimList(array $params,string $claimType) {
        $table = $this->orderClaim->table;
        $orderTable = config('tables.order');

        if(empty($params['ostatus']) || $params['ostatus']=='FA') {
            if($params['ostatus']=='FA')$claimType = 'FA';
            switch($claimType) {
                case 'cancle':
                    $checkTypes = config('order.cancleStatus');
                break;
                case 'return':
                    $checkTypes = config('order.returnStatus');
                break;
                case 'refund':
                    $checkTypes = config('order.refundStatus');
                break;
                case 'exchange':
                    $checkTypes = config('order.exchangeStatus');
                break;
                case 'FA':
                   $checkTypes = ['FA'=>1,'RA'=>1,'CA'=>1]; //,'EA'=>1
                break;
            }
            $whereData = [];
            $whereVals = [];

            foreach($checkTypes as $key=>$val) {
                $whereData[] = $table.'.claimType = ?';
                $whereVals[] = $key;
            }
            $whereString = '('.implode(' OR ',$whereData).')';

        } else {
            if($params['ostatus']=='FC') {
                $whereVals = ['RC','CC'];
                $whereString = $table.'.claimType = ? OR '.$table.'.claimType = ?';
            } else {
                $whereString = $table.'.claimType = ?';
                $whereVals = [$params['ostatus']];
            }
        }

        $select = [
                $table.'.*',
                $orderTable.'.oname',
                $orderTable.'.opcs',
                $orderTable.'.rname',
                $orderTable.'.rpcs',
                $orderTable.'.order_code',
                $orderTable.'.rpost',
                $orderTable.'.raddr1',
                $orderTable.'.raddr2',
                $orderTable.'.paymethod',
                $orderTable.'.usePoint',
                $orderTable.'.useCouponId',
                $orderTable.'.useCouponPrice',
                $orderTable.'.created_at AS orderDate',
                $orderTable.'.price AS totalPrice',
                $orderTable.'.deliveryPrice',
            ];
        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->orderClaim->setQueryParams($params);

        $list = $this->orderClaim::orderBy($table.'.'.$orderByField,$orderBySort)
                     ->select($select)
                     ->leftJoin($orderTable,$orderTable.'.id','=',$table.'.oid')
                     ->whereRaw($whereString,$whereVals)
                     ->where(function($query) {
                         $table = $this->orderClaim->table;
                         $orderTable = config('tables.order');
                         $queryParams = $this->orderClaim->queryParams;
                         if(!empty($queryParams['keyword'])) {
                             switch($queryParams['keywordCmd']) {
                                case 'rname':
                                case 'rpcs':
                                case 'oname':
                                case 'opcs':
                                case 'order_code':
                                    $query->where($orderTable.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                                break;
                             }

                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where($table.'.created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($table.'.created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                         if(!empty($queryParams['paymethod'])) {
                            $pstatuses = explode(',',$queryParams['paymethod']);
                            $queryData = [];
                            $vals = [];
                            foreach($pstatuses as $val) {
                                $queryData[] = $orderTable.'.paymethod = ?';
                                $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }
                     })
                    ->paginate($params['limit']);

        $resultList = [];
        $cprTable = $this->orderClaimProduct->table;
        $opTable = config('tables.orderProduct');

        foreach($list as $val) {
            $val->productList =$this->orderClaimProduct::orderBy($cprTable.'.id','desc')
                            ->select($opTable.'.*')
                            ->leftJoin($opTable,$opTable.'.id','=',$cprTable.'.opid')
                            ->where($cprTable.'.claim_id',$val->id)
                            ->get();

        }
        return $list;
    }

    public function getClaimDataListByIds(array $ids) {
        $cprTable = $this->orderClaimProduct->table;
        $table = $this->orderClaim->table;
        $select = [
               $cprTable.'.opid',
               $table.'.claimType',
               $table.'.oid',
               $table.'.id',
               $table.'.oldOstatus',
               $table.'.recoverPrice',// 환불 금액
               $table.'.recoverPoint',// 환불포인트
               $table.'.recoverCouponId',// 복구 쿠폰
        ];

        return $this->orderClaim::whereIn($table.'.id',$ids)
                                  ->select($select)
                                  ->leftJoin($cprTable,$cprTable.'.claim_id','=',$table.'.id')
                                  ->get();
    }

    public function getClaimDataListById(int $id) {
        $cprTable = $this->orderClaimProduct->table;
        $table = $this->orderClaim->table;
        $select = [
               $cprTable.'.opid',
               $table.'.*'
        ];

        return $this->orderClaim::where($table.'.id',$id)
                                  ->select($select)
                                  ->leftJoin($cprTable,$cprTable.'.claim_id','=',$table.'.id')
                                  ->get();
    }
    public function updateClaimStatus(array $ids,array $claimParams) {
        return $this->orderClaim::whereIn('id',$ids)->update($claimParams);
    }


}

