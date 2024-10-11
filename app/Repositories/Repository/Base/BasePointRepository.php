<?php
namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizePoint;
use App\Models\Customize\CustomizeMember;
class BasePointRepository  {

    protected $point;
    protected $member;

    public $useFields;
    public $PCODES;

    public function __construct(CustomizePoint $point,CustomizeMember $member) {
        $this->point = $point;
        $this->useFields  = $this->point->useFields;
        $this->PCODES = $this->point->PCODES;
        $this->member = $member;
    }

    // 포인트 리스트
    public function getPointList(array $params) {
        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->point->setQueryParams($params);

        if($orderByField=='name') {
            $orderByField = $this->member->table.'.'.$orderByField;
        } else {
            $orderByField = $this->point->table.'.'.$orderByField;
        }
        $list = $this->point::orderBy($orderByField,$orderBySort)
                     ->select($this->point->table.'.*',$this->member->table.'.name',$this->member->table.'.uid')
                     ->leftJoin($this->member->table,$this->member->table.'.id','=',$this->point->table.'.user_id')
                     ->where(function($query) {
                         $queryParams = $this->point->queryParams;
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
                         if(!empty($queryParams['pcode'])) {
                             $query->where('pcode',$queryParams['pcode']);
                         }
                         if(!empty($queryParams['ptype'])) {
                            $query->where('ptype',$queryParams['ptype']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where($this->point->table.'.created_at','>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($this->point->table.'.created_at','<=', $queryParams['endate'].' 23:59:59');
                            }
                         }


                     })
                     ->paginate($params['limit']);


        return $list;
    }


    //등록
    public function insertPoint(array $fieldsets) {
        $insData = $this->point::create($fieldsets);
        return $insData;
    }

}

