<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseOrderReviewRepository;



class CoreOrderReviewRepository extends  BaseOrderReviewRepository {



    // 상품후기 목록
    public function getReviewDataList(array $params,int $limit) {

        $orTable = $this->orderReview->table;
        $ptTable = config('tables.product');
        $memTable = config('tables.users');

        if(!empty($params['orderByField'])) {
            switch($params['orderByField']) {
                case 'id':
                case 'created_at':
                case 'grade':
                    $orderByField = $orTable.'.'.$params['orderByField'];
                break;
                case 'user_name':
                    $orderByField = $memTable.'.name';
                break;
                case 'pname':
                    $orderByField = $ptTable.'.pname';
                break;
            }
        } else $orderByField = $orTable.'.id';

        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->orderReview->setQueryParams($params);

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
            $memTable.'.name AS user_name'
        ];

        $list = $this->orderReview::orderBy($orderByField,$orderBySort)
                     ->leftJoin($ptTable,$ptTable.'.id','=',$orTable.'.pid')
                     ->leftJoin($memTable,$orTable.'.user_id','=',$memTable.'.id')
                     ->select($select)
                     ->where(function($query) {
                         $queryParams = $this->orderReview->queryParams;
                         $ptTable = config('tables.product');
                         $memTable = config('tables.users');
                         if(!empty($queryParams['keyword'])) {
                             switch($queryParams['keywordCmd']) {
                                case 'name':
                                    $query->where($memTable.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                                break;
                                case 'pname':
                                    $query->where($ptTable.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                                break;
                                case 'content':
                                    $query->where($this->orderReview->table.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                                break;

                             }

                         }
                         if(!empty($queryParams['photo']) && $queryParams['photo']=='yes') {
                            $query->whereNotNull('imgs');
                         }

                         if(!empty($queryParams['category'])) {
                            $categorys = explode(',',$queryParams['category']);
                            switch(count($categorys)) {
                                case 1:
                                    $query->whereRaw('(find_in_set(?,category1)>0 OR find_in_set(?,category2)>0 OR find_in_set(?,category3)>0)',[$categorys[0],$categorys[0],$categorys[0]]);
                                break;
                                case 2:
                                    $query->whereRaw('(find_in_set(?,category1)=2 OR find_in_set(?,category2)=2 OR find_in_set(?,category3)=2)',[$categorys[1],$categorys[1],$categorys[1]]);
                                break;
                                default:
                                     $query->whereRaw('(category1=? OR category2=? OR category3=?)',[$queryParams['category'],$queryParams['category'],$queryParams['category']]);
                                break;
                            }
                         }
                         if(!empty($queryParams['brand'])) {
                             $query->where('brandId',$queryParams['brand']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where($this->orderReview->table.'.created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($this->orderReview->table.'.created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                        /*
                         if(!empty($queryParams['pstatus'])) {
                            $pstatuses = explode(',',$queryParams['pstatus']);
                            $queryData = [];
                            $vals = [];
                            foreach($pstatuses as $val) {
                                $queryData[] = 'pstatus = ?';
                                $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }
                         */


                     })
                     ->paginate($limit);


        return $list;
    }

    public function blindReviews(array $ids,string $cmd) {
        return $this->orderReview::whereIn('id',$ids)->update(['is_delete'=>$cmd]);
    }
}

