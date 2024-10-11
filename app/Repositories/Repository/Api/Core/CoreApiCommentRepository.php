<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseCommentRepository;

class CoreApiCommentRepository extends BaseCommentRepository {


    //
    public function getCommentInfo(int $id) {

        return $this->comment::find($id);
    }

    // 정보 리스트
    public function getCommentList(array $params) {
        $parentId = $params['parentId'];
        $parentType = $params['parentType'];
        $limit = (!empty($params['limit']))?$params['limit']:20;
        $memTable = config('tables.users');
        $table = $this->comment->table;
        $data = $this->comment::where($table.'.parentId',$parentId)
                                    ->select([$table.'.*',$memTable.'.name as memName',$memTable.'.nick as memNick',$memTable.'.img as memImg'])
                                    ->leftJoin($memTable,$memTable.'.id','=',$table.'.user_id')
                                    ->where($table.'.parentType',$parentType)
                                    ->where($table.'.depth',1)
                                    ->orderBy($table.'.id','desc')
                                    ->paginate($limit);

        foreach($data as $val) {
            $val->subList = $this->comment::where($table.'.parentId',$parentId)
                                            ->select([$table.'.*',$memTable.'.name as memName',$memTable.'.nick as memNick',$memTable.'.img as memImg'])
                                            ->leftJoin($memTable,$memTable.'.id','=',$table.'.user_id')
                                            ->where($table.'.parentType',$parentType)
                                            ->where($table.'.depth',2)
                                            ->where($table.'.pid',$val->id)
                                            ->orderBy('id','asc')->get();

        }
        return $data;
    }
    //등록
    public function insertComment(array $fieldsets) {
        $fieldsets['content'] = nl2br($fieldsets['content']);
        $insData = $this->comment::create($fieldsets);
        return $insData;
    }

    //수정
    public function updateComment(int $id,array $params) {
        if(!empty($params['content']))$params['content'] = nl2br($params['content']);
        $updData = $this->comment::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteComment(int $id) {
        $row = $this->comment::find($id);
        if($row && $row->depth) {
            $this->comment::find($id)->delete();
            if($row->depth==1) {
                $this->comment::where('pid',$id)->where('depth',2)->delete();
            }
        }

    }

}

