<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BasePointRepository;


class CoreApiPointRepository extends  BasePointRepository {


    // 포인트 리스트
    public function getMyPointList(int $user_id,array $params) {

        $this->point->setQueryParams($params);
        $list = $this->point::orderBy('id','DESC')
                     ->where('user_id',$user_id)
                     ->where(function($query) {
                         $queryParams = $this->point->queryParams;
                         if(!empty($queryParams['pcode'])) {
                             $query->where('pcode',$queryParams['pcode']);
                         }
                         if(!empty($queryParams['ptype'])) {
                            $query->where('ptype',$queryParams['ptype']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where('created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where('created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                     })
                     ->paginate($params['limit']);


        return $list;
    }

}

