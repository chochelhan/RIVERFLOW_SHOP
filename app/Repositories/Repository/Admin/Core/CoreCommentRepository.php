<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseCommentRepository;

class CoreCommentRepository extends BaseCommentRepository {


    // 정보 리스트
    public function getCommentList(int $parentId,string $parentType) {
        $results = $this->comment::where('parentId',$parentId)->where('parentType',$parentType)->orderBy('id','asc')->get();
        return $results;
    }
    //등록
    public function insertComment(array $fieldsets) {
        $insData = $this->comment::create($fieldsets);
        return $insData;
    }

    //수정
    public function updateComment(int $id,array $params) {

        $updData = $this->comment::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //blind
    public function blindComment(int $id,string $cmd) {
        $row =$this->board::find($id)->update(['is_delete'=>$cmd]);
        return $row;
    }

}

