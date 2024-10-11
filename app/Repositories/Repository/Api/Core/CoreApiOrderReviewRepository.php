<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseOrderReviewRepository;


class CoreApiOrderReviewRepository extends  BaseOrderReviewRepository {

    public function insertOrderReview(array $fieldsets) {
        $insData = $this->orderReview::create($fieldsets);
        return $insData;
    }

    public function getIsOrderReview(int $user_id,int $oid,int $pid) {
        return $this->orderReview::where('oid',$oid)->where('user_id',$user_id)->where('pid',$pid)->first();
    }

    public function getMyReviewList(int $user_id,array $params) {

        $this->orderReview->setQueryParams($params);
        return $this->orderReview::orderBy('id','desc')
                                ->where('user_id',$user_id)
                                ->where(function($query) {
                                   $queryParams = $this->orderReview->queryParams;
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
    }

    public function getReviewListByPid(int $pid,int $limit) {

        $orTable = $this->orderReview->table;
        $ptTable = config('tables.product');
        $memTable = config('tables.users');
        $select = [
            $orTable.'.*',
            $ptTable.'.pname',
            $ptTable.'.listImg',
            $ptTable.'.category1',
            $ptTable.'.category2',
            $ptTable.'.category3',
            $ptTable.'.brandId',
            $ptTable.'.price',
            $ptTable.'.dcprice',
            $memTable.'.name AS user_name',
            $memTable.'.img AS userImg'
        ];
        return $this->orderReview::orderBy($orTable.'.id','desc')
                                ->where($orTable.'.pid',$pid)
                                ->where('is_delete','!=','yes')
                                ->leftJoin($ptTable,$ptTable.'.id','=',$orTable.'.pid')
                                ->leftJoin($memTable,$orTable.'.user_id','=',$memTable.'.id')
                                ->select($select)
                                ->paginate($limit);
    }
}

