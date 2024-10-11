<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Models\Customize\CustomizeOrder;
use App\Models\Customize\CustomizeOrderProduct;
use App\Models\Customize\CustomizeMember;
use App\Models\Customize\CustomizeProduct;


class CoreOrderStatisticsRepository  {

    protected $order;
    protected $member;
    protected $orderProduct;
    protected $product;


    public function __construct(CustomizeOrder $order,CustomizeOrderProduct $orderProduct,
                                CustomizeMember $member,CustomizeProduct $product) {

        $this->order = $order;
        $this->orderProduct = $orderProduct;
        $this->member = $member;
        $this->product = $product;

    }
    // 회원가입 통계
    public function getMemberStatistics(array $params) {

        $where = '';
        $groupBy = '';
        switch($params['dateType']) {
            case 'day':
                $where = '(substring(created_at,1,7)>=? AND substring(created_at,1,7)<=?)';
                $value = [$params['month'],$params['month']];
                $select = 'count(id) as total,substring(created_at,9,2) as dateGroup';
                break;
            case 'month':
                $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                $value = [$params['styear'],$params['enyear']];
                $select = 'count(id) as total,substring(created_at,6,2) as dateGroup';
                break;
            default:
                $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                $value = [$params['styear'],$params['enyear']];
                $select = 'count(id) as total,substring(created_at,1,4) as dateGroup';
                break;
        }
        return $this->member::whereRaw($where,$value)->where('admin','no')->groupBy('dateGroup')->selectRaw($select)->get();
    }

    /**
     * 전체 주문 통계
     *
     **/
    public function getOrderStatistics(array $params) {
        list($where,$value,$select) = $this->getDateTypeParams($params);
        /// 주문건수냐 주문금액이냐에 따라
        if($params['type']=='price') {
            return $this->orderProduct::whereRaw($where,$value)
                                            ->where('ostatus','OC')
                                            ->groupBy('dateGroup')
                                            ->selectRaw($select)->get();
        } else {
            $list = $this->orderProduct::whereRaw($where,$value)
                                            ->where('ostatus','OC')
                                            ->groupBy('dateGroup')
                                            ->groupBy('oid')
                                            ->selectRaw($select)->get();
            $checkDates = [];
            foreach($list as $val) {
                if(empty($checkDates[$val->dateGroup])) {
                    $checkDates[$val->dateGroup] = 1;
                    $val->total = 1;
                } else $checkDates[$val->dateGroup] = $checkDates[$val->dateGroup]+1;
            }
            $result = [];
            foreach($checkDates as $key=>$val) {
                $result[] = ['dateGroup'=>$key,'total'=>$val];
            }
            return $result;
        }


    }

    /**
     *회원별 주문 통계
     *
     **/
    public function getMemberList() {
        return $this->member::orderBy('name','asc')->where('admin','no')->where('mstatus','!=','out')->get();

    }
    public function getOrderMemberStatistics(array $params) {
        $data = [];
        if(empty($params['user_id'])) {
            $where = '';
            switch($params['dateType']) {
                case 'day':
                    $where = '(substring(created_at,1,7)>=? AND substring(created_at,1,7)<=?)';
                    break;
                default:
                   $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                    break;
            }
             $value = [$params['styear'],$params['enyear']];
            if($params['type']=='price') {
                $select = 'user_id,sum(payprice) total';

            } else  {
                $select = 'user_id,count(user_id) total';
            }
            /// 최고 매출 회원 10명의 회원데이터를 가져온다
            $userList = $this->orderProduct::whereRaw($where,$value)
                                    ->whereNotNull('user_id')
                                    ->where('ostatus','OC')
                                    ->groupBy('user_id')
                                    ->orderBy('total','desc')
                                    ->limit(10)
                                    ->selectRaw($select)->get();


            ////// 정해진 기간의 매출액을 가져온다
            foreach($userList as $val) {
                $params['user_id'] = $val->user_id;
                $datas = $this->getOrderMemberByUserId($params);
                $data[] = ['user_id'=>$val->user_id,'datas'=>$datas];
            }
        } else {
            $dataList = $this->getOrderMemberByUserId($params);
            $data[] = ['user_id'=>$params['user_id'],'datas'=>$dataList];
        }
        return $data;
    }

    private function getOrderMemberByUserId(array $params) {

        list($where,$value,$select) = $this->getDateTypeParams($params);
        if($params['type']=='price') {
            return $this->orderProduct::whereRaw($where,$value)
                                    ->where('ostatus','OC')
                                    ->where('user_id',$params['user_id'])
                                    ->groupBy('dateGroup')->selectRaw($select)->get();
        } else {
            $list = $this->orderProduct::whereRaw($where,$value)
                                        ->where('ostatus','OC')
                                        ->where('user_id',$params['user_id'])
                                        ->groupBy('dateGroup')
                                        ->groupBy('oid')
                                        ->selectRaw($select)->get();


            $checkDates = [];
            foreach($list as $val) {
                if(empty($checkDates[$val->dateGroup])) {
                    $checkDates[$val->dateGroup] = 1;
                    $val->total = 1;
                } else $checkDates[$val->dateGroup] = $checkDates[$val->dateGroup]+1;
            }
            $result = [];
            foreach($checkDates as $key=>$val) {
                $result[] = ['dateGroup'=>$key,'total'=>$val];
            }
            return $result;
        }
    }

    /**
    * 상품별 주문 통계
    *
    **/
    public function getProductList() {
        return $this->product::orderBy('pname','asc')->get();

    }

    public function getOrderProductStatistics(array $params) {
        $data = [];
        if(empty($params['pid'])) {
            $where = '';
            switch($params['dateType']) {
                case 'day':
                    $where = '(substring(created_at,1,7)>=? AND substring(created_at,1,7)<=?)';
                    break;
                default:
                    $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                    break;
            }
            $value = [$params['styear'],$params['enyear']];
            if($params['type']=='price') {
                $select = 'pid,sum(payprice) total';
            } else  {
                $select = 'pid,count(pid) total';
            }
             /// 최고 매출 상품 10개의 상품데이터를 가져온다
            $userList = $this->orderProduct::whereRaw($where,$value)
                        ->groupBy('pid')
                        ->where('ostatus','OC')
                        ->orderBy('total','desc')
                        ->limit(10)
                        ->selectRaw($select)->get();

            ////// 정해진 기간의 매출액을 가져온다
            foreach($userList as $val) {
                $params['pid'] = $val->pid;
                $datas = $this->getOrderProductByPid($params);
                $data[] = ['pid'=>$val->pid,'datas'=>$datas];
            }
        } else {
            $dataList = $this->getOrderProductByPid($params);
            $data[] = ['pid'=>$params['pid'],'datas'=>$dataList];
        }
        return $data;
    }

    private function getOrderProductByPid(array $params) {

        list($where,$value,$select) = $this->getDateTypeParams($params);
        if($params['type']=='price') {
            return $this->orderProduct::whereRaw($where,$value)
                                    ->where('ostatus','OC')
                                    ->where('pid',$params['pid'])
                                    ->groupBy('dateGroup')->selectRaw($select)->get();
        } else {
            $list =  $this->orderProduct::whereRaw($where,$value)
                                                ->where('ostatus','OC')
                                                ->where('pid',$params['pid'])
                                                ->groupBy('dateGroup')
                                                ->groupBy('oid')
                                                ->selectRaw($select)->get();
            $checkDates = [];
            foreach($list as $val) {
                if(empty($checkDates[$val->dateGroup])) {
                    $checkDates[$val->dateGroup] = 1;
                    $val->total = 1;
                } else $checkDates[$val->dateGroup] = $checkDates[$val->dateGroup]+1;
            }
            $result = [];
            foreach($checkDates as $key=>$val) {
                $result[] = ['dateGroup'=>$key,'total'=>$val];
            }
            return $result;

        }
    }

    private function getDateTypeParams($params) {
        switch($params['dateType']) {
            case 'day':
                $where = '(substring(created_at,1,7)>=? AND substring(created_at,1,7)<=?)';
                $value = [$params['month'],$params['month']];
                if($params['type']=='price')$select = 'sum(payprice) as total,substring(created_at,9,2) as dateGroup';
                else $select = 'oid as total,substring(created_at,9,2) as dateGroup';
                break;
            case 'month':
                $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                $value = [$params['styear'],$params['enyear']];
                if($params['type']=='price')$select = 'sum(payprice) as total,substring(created_at,6,2) as dateGroup';
                else $select = 'oid as total,substring(created_at,6,2) as dateGroup';
                break;
            default:
                $where = '(substring(created_at,1,4)>=? AND substring(created_at,1,4)<=?)';
                $value = [$params['styear'],$params['enyear']];
                if($params['type']=='price')$select = 'sum(payprice) as total,substring(created_at,1,4) as dateGroup';
                else $select = 'oid as total,substring(created_at,1,4) as dateGroup';
                break;
        }

        return [$where,$value,$select];
    }

}

