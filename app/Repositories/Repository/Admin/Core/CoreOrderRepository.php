<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseOrderRepository;



class CoreOrderRepository extends  BaseOrderRepository {



    public function getOrderList(array $params) {

        $oids = '';
        if((!empty($params['keyword']) && $params['keywordCmd'] == 'pname') || !empty($params['ostatus'])) {
            $keyword = (!empty($params['keyword']))?$params['keyword']:'';
            $ostatus = (!empty($params['ostatus']))?$params['ostatus']:'';

            $productList = $this->orderProduct::select('oid')
                ->distinct()
                ->when($keyword,function($query,$keyword) {
                   $query->where('pname','like','%'.$keyword.'%');
                })
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

        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->order->setQueryParams($params);

        $list = $this->order::orderBy($orderByField,$orderBySort)
                     ->when($oids,function($query,$oids) {
                         if($oids[0]=='not') {
                            $query->where('id','<',1);
                         } else {
                            $query->whereIn('id',$oids);
                         }
                     })
                     ->where(function($query) {
                         $queryParams = $this->order->queryParams;
                         if(!empty($queryParams['keyword']) && $queryParams['keywordCmd']!='pname') {
                              $query->where($queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                         }
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


        foreach($list as $key=>$val) {
            $orderProducts = $this->orderProduct::where('oid',$val->id)->get();
            $val['orderProducts'] = $orderProducts;
            $list[$key] = $val;
        }

        return $list;
    }

    // oids 로 주문가져오기
    public function getOrderListByIds(array $ids) {

        $table = $this->order->table;
        $opTable = $this->orderProduct->table;
        $select = [
            $table.'.*',
            $opTable.'.id as opId',
            $opTable.'.ostatus as opOstatus'
        ];
        return $this->order::whereIn($table.'.id',$ids)
                             ->select($select)
                             ->leftJoin($opTable,$opTable.'.oid','=',$table.'.id')
                             ->get();
    }

    // 주문에 연관된 주문상품 가져오기
    public function getOrderProductListByIds(array $ids) {
        return $this->orderProduct::orderBy('id','desc')->whereIn('id',$ids)->get();
    }

    // 주문상태 변경 (주문테이블)
    public function updateOrderStatus(int $id,string $ostatus) {
        return $this->order::find($id)->update(['ostatus'=>$ostatus]);

    }
    //주문상태 변경 (주문상품 테이블)
    public function updateOrderProductStatus(array $ids,array $params) {
        return $this->orderProduct::whereIn('id',$ids)->update($params);

    }

    //주문상태 변경 (주문상품 테이블 한개만 변경)
    public function updateOrderProductStatusBySingle(int $id,string $ostatus) {
        return $this->orderProduct::find($id)->update(['ostatus'=>$ostatus]);
    }




    /// 주문 상태 히스토리 목록
    public function getOrderHistory(int $oid) {
        return $this->orderHistory::orderBy($this->orderHistory->table.'.id','DESC')
                        ->select($this->orderHistory->table.'.*',$this->opTable.'.pname',$this->opTable.'.opt_name')
                        ->leftJoin($this->opTable,$this->opTable.'.id','=',$this->orderHistory->table.'.opid')
                        ->where($this->orderHistory->table.'.oid',$oid)
                        ->get();
    }

    public function getOrderCount(array $params) {

            return $this->orderProduct::whereIn('ostatus', $params['ostatus'])->whereDate($params['dateKey'],'>=',$params['dateValue'])->get();


    }
    public function getOrderAllCount(array $params) {

         return $this->order::whereDate($params['dateKey'],'>=',$params['dateValue'])->count();

    }
}

