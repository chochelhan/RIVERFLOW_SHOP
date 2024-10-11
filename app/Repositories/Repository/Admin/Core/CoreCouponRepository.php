<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseCouponRepository;


class CoreCouponRepository extends  BaseCouponRepository {


    //목록
    public function getCouponList(array $params,int $limit) {

        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->coupon->setQueryParams($params);
        $data = $this->coupon::orderBy($orderByField,$orderBySort)
                    ->where(function($query) {

                        $queryParams = $this->coupon->queryParams;
                        if(!empty($queryParams['keyword'])) {
                            $query->where($queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                        }
                        if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                           if(!empty($queryParams['stdate'])) {
                               $query->where($queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                           }
                           if(!empty($queryParams['endate'])) {
                               $query->where($queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                           }
                        }
                        if(!empty($queryParams['ctype'])) {
                            $ctypes = explode(',',$queryParams['ctype']);
                            $ctypeStrings = [];
                            $ctypeValues = [];
                            foreach($ctypes as $val) {
                                $ctypeStrings[] = 'ctype=?';
                                $ctypeValues[] = $val;
                            }
                            $ctypeString = '('.implode(' OR ',$ctypeStrings).')';
                            $query->whereRaw($ctypeString,$ctypeValues);
                        }
                        if(!empty($queryParams['pubtype'])) {
                            $pubtypes = explode(',',$queryParams['pubtype']);
                            $pubtypeStrings = [];
                            $pubtypeValues = [];
                            foreach($pubtypes as $val) {
                                $pubtypeStrings[] = 'pubtype=?';
                                $pubtypeValues[] = $val;
                            }
                            $pubtypeString = '('.implode(' OR ',$pubtypeStrings).')';
                            $query->whereRaw($pubtypeString,$pubtypeValues);
                        }
                        if(!empty($queryParams['cstatus'])) { // 쿠폰상태

                        }

                    })
                    ->paginate($limit);

        return $data;
    }
    public function getCouponUseList() {

        $date = date('Y-m-d');
        return $this->coupon::whereDate('pubStdate','<=',$date)->whereDate('pubEndate','>=',$date)->where('pubtype','direct')->orderBy('id','desc')->get();

    }
    public function getCouponSimpleList() {

        return $this->coupon::orderBy('id','desc')->select('cname','id')->get();

    }
    //등록
    public function insertCoupon(array $params) {
        $insData = $this->coupon::create($params);
        return $insData;
    }

    //수정
    public function updateCoupon(int $id,array $params) {

        $updData = $this->coupon::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteCoupon(int $id) {
        $delData = $this->coupon::destroy($id);
        return ($delData)?$id:'';
    }

    // 쿠폰 발행 등록
    public function insertCouponPublish(array $params) {
        $insData = $this->couponPublish::create($params);
        return $insData;
    }

    //쿠폰 발행 수정
    public function updateCouponPublish(int $id,array $params) {

        $updData = $this->couponPublish::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //쿠폰 발행 삭제
    public function deleteCouponPublish(int $id) {
        $delData = $this->couponPublish::destroy($id);
        return ($delData)?$id:'';
    }

    // 쿠폰 발행 목록
    public function getCouponPublishList(array $params,int $limit) {

        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->coupon->setQueryParams($params);
        if($orderByField=='name') {
            $orderByField = $this->member->table.'.'.$orderByField;
        } else {
            $orderByField = $this->couponPublish->table.'.'.$orderByField;
        }

        $data = $this->couponPublish::orderBy($orderByField,$orderBySort)
                    ->select($this->couponPublish->table.'.*',$this->member->table.'.name',$this->member->table.'.uid')
                    ->leftJoin($this->member->table,$this->member->table.'.id','=',$this->couponPublish->table.'.user_id')
                    ->where(function($query) {
                        $queryParams = $this->coupon->queryParams;
                        if(!empty($queryParams['keyword'])) {
                             switch($queryParams['keywordCmd']) {
                                case 'name':
                                    $query->where($this->member->table.'.name','like','%'.$queryParams['keyword'].'%');
                                break;
                                case 'uid':
                                    $query->where($this->member->table.'.uid','like','%'.$queryParams['keyword'].'%');
                                break;
                             }

                        }
                        if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                           if(!empty($queryParams['stdate'])) {
                               $query->where($this->couponPublish->table.'.'.$queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                           }
                           if(!empty($queryParams['endate'])) {
                               $query->where($this->couponPublish->table.'.'.$queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                           }
                        }
                        if(!empty($queryParams['ctype'])) {
                            $ctypes = explode(',',$queryParams['ctype']);
                            $ctypeStrings = [];
                            $ctypeValues = [];
                            foreach($ctypes as $val) {
                                $ctypeStrings[] = 'ctype=?';
                                $ctypeValues[] = $val;
                            }
                            $ctypeString = '('.implode(' OR ',$ctypeStrings).')';
                            $query->whereRaw($ctypeString,$ctypeValues);
                        }
                        if(!empty($queryParams['pubtype'])) {
                            $pubtypes = explode(',',$queryParams['pubtype']);
                            $pubtypeStrings = [];
                            $pubtypeValues = [];
                            foreach($pubtypes as $val) {
                                $pubtypeStrings[] = 'pubtype=?';
                                $pubtypeValues[] = $val;
                            }
                            $pubtypeString = '('.implode(' OR ',$pubtypeStrings).')';
                            $query->whereRaw($pubtypeString,$pubtypeValues);
                        }
                        if(!empty($queryParams['cuse'])) { // 사용여부
                            $query->where('cuse',$queryParams['cuse']);
                        }
                        if(!empty($queryParams['cid'])) { // 쿠폰
                            $query->where('cid',$queryParams['cid']);
                        }

                    })
                    ->paginate($limit);

        return $data;
    }
    // 쿠폰 총발행수량
    public function getPublishTotal(int $id) {
        return $this->couponPublish::where('cid',$id)->count();
    }
}

