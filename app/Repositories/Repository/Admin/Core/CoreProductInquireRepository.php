<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseProductInquireRepository;

class CoreProductInquireRepository extends BaseProductInquireRepository {


     // 상품문의 목록
    public function getProductInquireDataList(array $params,int $limit) {

        $table = $this->productInquire->table;
        $ptTable = config('tables.product');
        $memTable = config('tables.users');

        if(!empty($params['orderByField'])) {
            switch($params['orderByField']) {
                case 'id':
                case 'created_at':
                case 'status':
                    $orderByField = $table.'.'.$params['orderByField'];
                    break;
                case 'user_name':
                    $orderByField = $memTable.'.name';
                    break;
                case 'pname':
                    $orderByField = $ptTable.'.pname';
                    break;
            }
        } else $orderByField = $table.'.id';

        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->productInquire->setQueryParams($params);

        $select = [
            $table.'.*',
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

        $list = $this->productInquire::orderBy($orderByField,$orderBySort)
                     ->leftJoin($ptTable,$ptTable.'.id','=',$table.'.pid')
                     ->leftJoin($memTable,$table.'.user_id','=',$memTable.'.id')
                     ->select($select)
                     ->where(function($query) {
                         $queryParams = $this->productInquire->queryParams;
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
                                case 'subject':
                                    $query->where($this->productInquire->table.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                                break;
                             }

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
                               $query->where($this->productInquire->table.'.created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($this->productInquire->table.'.created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                         if(!empty($queryParams['status'])) {
                            $pstatuses = $queryParams['status'];
                            $queryData = [];
                            $vals = [];
                            foreach($pstatuses as $val) {
                                $queryData[] = $this->productInquire->table.'.status = ?';
                                $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }


                     })
                     ->paginate($limit);


        return $list;
    }
    // 상품문의 상세
    public function getProductInquireInfo(int $id) {
        $memTable = config('tables.users');
        return $this->productInquire::where($this->productInquire->table.'.id',$id)->select($this->productInquire->table.'.*',$memTable.'.name')
                                            ->leftJoin($memTable,$memTable.'.id','=',$this->productInquire->table.'.user_id')->first();
    }

    // 상품문의 답변
    public function updateProductInquire(int $id,array $fieldset) {
        return $this->productInquire::find($id)->update($fieldset);
    }

     // 상품문의 삭제
    public function deleteProductInquire(array $ids) {
        return $this->productInquire::whereIn('id',$ids)->delete();
    }


}

