<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseProductRepository;


class CoreApiProductRepository extends  BaseProductRepository {

    public $isLoginInfo;

    //목록
    public function getProductList(array $params,int $limit) {


        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $table = $this->product->table;
        $wTable = config('tables.wish');


        switch($orderByField) {
            case 'favorite':
                $orderByField = $table.'.wish';
            break;
            case 'lowprice':
                $orderByField = 'sortPrice';
                $orderBySort = 'asc';
            break;
            case 'highprice':
                $orderByField = 'sortPrice';
                $orderBySort = 'desc';
            break;
            default:
                $orderByField = $table.'.'.$orderByField;
            break;
        }

        $wTable = config('tables.wish');
        $select = 'if('.$table.'.dcprice>0,'.$table.'.dcprice,'.$table.'.price) AS sortPrice,';
        if(!empty($params['user_id'])) { // 관심상품
            $select.= $table.".*,(SELECT ".$wTable.".id FROM ".$wTable." WHERE ".$wTable.".pid = ".$table.".id
                                    AND type='product' AND ".$wTable.".user_id = '".$params['user_id']."') AS myWish";
        } else {
            $select.= $table.'.*';
        }
        $device = isMobile();
        $queryString = 'find_in_set(?,'.$table.'.platform)>0';
        $nowDate = date('Y-m-d');
        $this->product->setQueryParams($params);
        $list = $this->product::orderBy($orderByField,$orderBySort)
                     ->where($table.'.pstatus','!=','hidden')
                     ->whereRaw('(('.$table.'.salePeriod=? AND ('.$table.'.periodStdate <=? AND '.$table.'.periodEndate >=?)) OR '.$table.'.salePeriod=?)',['period',$nowDate,$nowDate,'every'])
                     ->selectRaw($select)
                     ->whereRaw($queryString,$device)
                     ->where(function($query) {
                            $this->whereQuery($query);
                     })
                     ->paginate($limit);


        return $list;
    }
    private function whereQuery($query) {

        $table = $this->product->table;
        $queryParams = $this->product->queryParams;
        if(!empty($queryParams['keyword'])) {
            $query->where($table.'.'.$queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
        }
        if(!empty($queryParams['searchKeyword'])) {
            $query->where($table.'.keyword','like','%'.$queryParams['searchKeyword'].'%')->orWhere($table.'.pname','like','%'.$queryParams['searchKeyword'].'%');
        
        }
        if(!empty($queryParams['category'])) {
           $categorys = explode(',',$queryParams['category']);
           switch(count($categorys)) {
               case 1:
                   $query->whereRaw('(find_in_set(?,'.$table.'.category1)>0 OR find_in_set(?,'.$table.'.category2)>0 OR find_in_set(?,'.$table.'.category3)>0)',[$categorys[0],$categorys[0],$categorys[0]]);
               break;
               case 2:
                   $query->whereRaw('(find_in_set(?,'.$table.'.category1)=2 OR find_in_set(?,'.$table.'.category2)=2 OR find_in_set(?,'.$table.'.category3)=2)',[$categorys[1],$categorys[1],$categorys[1]]);
               break;
               default:
                    $query->whereRaw('('.$table.'.category1=? OR '.$table.'.category2=? OR '.$table.'.category3=?)',[$queryParams['category'],$queryParams['category'],$queryParams['category']]);
               break;
           }
        }

        if(!empty($queryParams['notInId'])) {
            $query->whereNotIn($table.'.id',$queryParams['notInId']);
        }
        if(!empty($queryParams['brandId'])) {
            $query->where($table.'.brandId',$queryParams['brandId']);
        }
        /// 가격대로 상품 찾기
        if(!empty($queryParams['minPrice']) && !empty($queryParams['maxPrice'])) {
            $query->whereRaw('(('.$table.'.dcprice > 0 AND ('.$table.'.dcprice >=? AND '.$table.'.dcprice <=?))
                              OR ('.$table.'.dcprice < 1 AND ('.$table.'.price >=? AND '.$table.'.price <=?)))',[$queryParams['minPrice'],$queryParams['maxPrice'],
                                $queryParams['minPrice'],$queryParams['maxPrice']]);
        }

    }



    // ids 값으로 상품목록 가져오기
    public function getProductListByIds(array $ids) {
        return $this->product::whereIn('id',$ids)->orderBy('id','desc')->get();
    }
    // ids 값으로 상품목록 가져오기
    public function getProductListSaleByIds(array $ids) {
        $device = isMobile();
        $queryString = 'find_in_set(?,platform)>0';
        $nowDate = date('Y-m-d');
        return $this->product::whereIn('id',$ids)
                      ->whereRaw('((salePeriod=? AND (periodStdate <=? AND periodEndate >=?)) OR salePeriod=?)',['period',$nowDate,$nowDate,'every'])
                      ->whereRaw($queryString,$device)
                      ->where('pstatus','!=','hidden')
                      ->orderBy('id','desc')->get();
    }

    // 상품정보 업데이트
    public function updateProduct(int $id,array $params) {
        return $this->product::find($id)->update($params);
    }
    // 상품옵션정보 업데이트
    public function updateProductOption(int $id,array $params) {
        return $this->productOption::find($id)->update($params);
    }
    public function getUseOptionInfoByPid(int $pid) {
        return $this->productOption::where('pid',$pid)->where('ouse','Y')->get();

    }

    /// 관심상품 총 수
    public function getMyWishCount(int $user_id) {

         $pTable = $this->product->table;
         $wTable = config('tables.wish');
         $device = isMobile();
         $queryString = 'find_in_set(?,platform)>0';
         $nowDate = date('Y-m-d');

         return $this->product::leftJoin($wTable,$wTable.'.pid','=',$pTable.'.id')
                              ->whereRaw('(('.$pTable.'.salePeriod=? AND ('.$pTable.'.periodStdate <=? AND '.$pTable.'.periodEndate >=?)) OR '.$pTable.'.salePeriod=?)',['period',$nowDate,$nowDate,'every'])
                              ->whereRaw($queryString,$device)
                              ->where($pTable.'.pstatus','!=','hidden')
                              ->where($wTable.'.user_id',$user_id)->count();
    }
}

