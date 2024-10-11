<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseProductRepository;


class CoreProductRepository extends  BaseProductRepository {


    //목록
    public function getProductList(array $params,int $limit) {

        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->product->setQueryParams($params);
        $list = $this->product::orderBy($orderByField,$orderBySort)
                     ->where(function($query) {
                         $queryParams = $this->product->queryParams;
                         if(!empty($queryParams['keyword'])) {
                             $query->where($queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
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
                         if(!empty($queryParams['optionUse'])) {
                            $query->where('optionUse',$queryParams['optionUse']);
                         }

                         if(!empty($queryParams['brand'])) {
                             $query->where('brandId',$queryParams['brand']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            switch($queryParams['dateCmd']) {
                                case 'periodStdate':
                                case 'periodEndate':
                                    $query->where('salePeriod','period');
                                break;
                            }
                            if(!empty($queryParams['stdate'])) {
                               $query->where($queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                         if(!empty($queryParams['platform'])) {
                            $platforms = explode(',',$queryParams['platform']);
                            $queryData = [];
                            $vals = [];
                            foreach($platforms as $val) {
                                 $queryData[] = 'find_in_set(?,platform)>0';
                                 $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }
                         if(!empty($queryParams['pstatus'])) {
                            $pstatuses = explode(',',$queryParams['pstatus']);
                            $queryData = [];
                            $vals = [];
                            $periodCheck = false;
                            foreach($pstatuses as $val) {
                                if($val=='sale')$periodCheck = true;
                                $queryData[] = 'pstatus = ?';
                                $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                            if(!empty($queryParams['dateCmd']) && $queryParams['dateCmd'] != 'created_at') {
                               $periodCheck = false;
                            }
                            if($periodCheck) {
                                $nowDate = date('Y-m-d');
                                $query->where('salePeriod','every');
                                $query->orWhere('salePeriod','period')
                                    ->where('periodStdate','<=',$nowDate.' 00:00:00')
                                    ->where('periodEndate','>=',$nowDate.' 23:59:59');
                            }
                         }


                     })
                     ->paginate($limit);


        return $list;
    }
    // ids 값으로 상품목록 가져오기
    public function getProductListByIds(array $ids) {
        return $this->product::whereIn('id',$ids)->orderBy('id','desc')->get();
    }

    
    //등록
    public function insertProduct(array $params) {
        $insData = $this->product::create($params);
        return $insData;
    }

    //수정
    public function updateProduct(int $id,array $params) {

        $updData = $this->product::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteProduct(int $id) {
        $delData = $this->product::destroy($id);
        return ($delData)?$id:'';
    }

    // 옵션 저장
    public function insertProductOption(array $params) {
        return $this->productOption::create($params);

    }
    // 옵션 수정
    public function updateProductOption(int $id,array $params) {
        $this->productOption::find($id)->update($params);
    }


    // 재고목록
    public function getProductInventoryList(array $params,int $limit) {

        $table = $this->product->table;
        $ivtTable = config('tables.productInventory');

        if(empty($params['orderByField'])) {
            $orderByField = $table.'.id';
        } else {
            switch($params['orderByField']) {
                case 'able_amt':
                case 'total_amt':
                case 'disable_amt':
                case 'sale_amt':
                    $orderByField = $ivtTable.'.'.$params['orderByField'];
                    break;
                default:
                    $orderByField = $table.'.'.$params['orderByField'];
                    break;
            }
        }

        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->product->setQueryParams($params);
        $whereString = $table.'.optionUse = ? OR ('.$table.'.optionUse=? AND '.$table.'.optionType=?)';
        $values = ['no','yes','single'];

        $list = $this->product::orderBy($orderByField,$orderBySort)
                     ->select($table.'.*',$ivtTable.'.able_amt',$ivtTable.'.disable_amt',$ivtTable.'.total_amt',$ivtTable.'.sale_amt',$ivtTable.'.id AS ivtId')
                     ->leftJoin($ivtTable,$ivtTable.'.pid','=',$table.'.id')
                     ->whereRaw($whereString,$values)
                     ->whereNull($ivtTable.'.oid')
                     ->where(function($query) {
                         $table = $this->product->table;
                         $ivtTable = config('tables.productInventory');
                         $queryParams = $this->product->queryParams;
                         if(!empty($queryParams['keyword'])) {
                             $query->where($table.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
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
                         if(!empty($queryParams['soldout']) && $queryParams['soldout']=='yes') {
                             $query->where($ivtTable.'.able_amt','<',1);

                         }

                         if(!empty($queryParams['brand'])) {
                             $query->where('brandId',$queryParams['brand']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            switch($queryParams['dateCmd']) {
                                case 'periodStdate':
                                case 'periodEndate':
                                    $query->where('salePeriod','period');
                                break;
                            }
                            if(!empty($queryParams['stdate'])) {
                               $query->where($table.'.'.$queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($table.'.'.$queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                         if(!empty($queryParams['platform'])) {
                            $platforms = explode(',',$queryParams['platform']);
                            $queryData = [];
                            $vals = [];
                            foreach($platforms as $val) {
                                 $queryData[] = 'find_in_set(?,platform)>0';
                                 $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }
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


                     })
                     ->paginate($limit);


        return $list;
    }

    // 옵션 재고목록
    public function getOptionInventoryList(array $params,int $limit) {

        $opTable = $this->productOption->table;
        $table = $this->product->table;
        $ivtTable = config('tables.productInventory');

        if(empty($params['orderByField'])) {
            $orderByField = $table.'.id';
        } else {
            switch($params['orderByField']) {
                case 'able_amt':
                case 'total_amt':
                case 'disable_amt':
                case 'sale_amt':
                    $orderByField = $ivtTable.'.'.$params['orderByField'];
                    break;
                default:
                    $orderByField = $table.'.'.$params['orderByField'];
                    break;
            }
        }

        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->product->setQueryParams($params);


        $select = [
            $opTable.'.*',
            $ivtTable.'.able_amt',
            $ivtTable.'.disable_amt',
            $ivtTable.'.total_amt',
            $ivtTable.'.sale_amt',
            $ivtTable.'.id AS ivtId',
            $table.'.pname',
            $table.'.listImg',
            $table.'.brandId',
            $table.'.category1',
            $table.'.category2',
            $table.'.category3',
            $table.'.serviceType',
            $table.'.optionType',
            $table.'.optionUse',
        ];


        $list = $this->productOption::orderBy($orderByField,$orderBySort)
                     ->select($select)
                     ->leftJoin($ivtTable,$ivtTable.'.oid','=',$opTable.'.id')
                     ->leftJoin($table,$opTable.'.pid','=',$table.'.id')
                     ->where(function($query) {
                         $table = $this->product->table;
                         $ivtTable = config('tables.productInventory');
                         $queryParams = $this->product->queryParams;
                         if(!empty($queryParams['keyword'])) {
                             $query->where($table.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
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
                         if(!empty($queryParams['soldout']) && $queryParams['soldout']=='yes') {
                             $query->where($ivtTable.'.able_amt','<',1);

                         }
                         if(!empty($queryParams['brand'])) {
                             $query->where('brandId',$queryParams['brand']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            switch($queryParams['dateCmd']) {
                                case 'periodStdate':
                                case 'periodEndate':
                                    $query->where('salePeriod','period');
                                break;
                            }
                            if(!empty($queryParams['stdate'])) {
                               $query->where($table.'.'.$queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($table.'.'.$queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                         if(!empty($queryParams['platform'])) {
                            $platforms = explode(',',$queryParams['platform']);
                            $queryData = [];
                            $vals = [];
                            foreach($platforms as $val) {
                                 $queryData[] = 'find_in_set(?,platform)>0';
                                 $vals[] = $val;
                            }
                            $queryString = '('.implode(' OR ',$queryData).')';
                            $query->whereRaw($queryString,$vals);
                         }
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


                     })
                     ->paginate($limit);


        return $list;
    }
}

