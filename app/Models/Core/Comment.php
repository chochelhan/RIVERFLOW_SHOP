<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Comment  extends Model {

    public $table;
    protected $fillable = [
        'user_id', //회원고유키
        'name', // 회원이름
        'parentId', // 댓글 최상위 글의 고유키
        'parentType', // 최상위 글의 구분값 (review: 후기글 , board: 게시판,event: 이벤트, special:기획전)
        'content', // 내용
        'depth', // 일반 1, 댓글의 댓글 2
        'pid', // 댓글의 댓글일경우 상위댓글 고유키
        'is_delete' // 블라인트 처리시 yes or no
    ];
    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.comment');
        foreach($this->fillable as $key) {
            if($key == 'is_delete')continue;
            $this->useFields[$key] = $key;
        }
    }

}
