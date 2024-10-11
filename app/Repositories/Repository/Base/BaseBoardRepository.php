<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\BoardRepositoryInterface;
use App\Models\Customize\CustomizeBoard;
use App\Models\Customize\CustomizeBoardArticle;
use App\Models\Customize\CustomizeSettingSite;
use App\Models\Customize\CustomizeComment;

class BaseBoardRepository implements BoardRepositoryInterface {

    protected $board;
    protected $boardArticle;
    protected $comment;
    public $filePath = 'public/board';
    public $imgUrl = '/boardImages/';

    public $useFields;
    public $articleUseFields;
    public $articleImgSize = ['width'=>640,'height'=>640];

    public function __construct(CustomizeBoard $board,CustomizeBoardArticle $boardArticle,CustomizeComment $comment) {
        $this->board = $board;
        $this->comment = $comment;

        $this->boardArticle = $boardArticle;
        $this->articleUseFields = $this->boardArticle->useFields;
        $this->useFields = $this->board->useFields;



    }

    public function getBoardInfo(int $id) {
        return $this->board::find($id);
    }

    public function getArticleInfo($id) {
        $memTable = config('tables.users');

        return $this->boardArticle::where($this->boardArticle->table.'.id',$id)
                                    ->leftJoin($memTable,$memTable.'.id','=',$this->boardArticle->table.'.user_id')
                                    ->select($this->boardArticle->table.'.*',$memTable.'.name',$memTable.'.nick')
                                    ->first();
    }

    // 게시글 리스트
    public function getArticleList(array $params) {

        $memTable = config('tables.users');

        $orderByField = (!empty($params['orderByField']))?$params['orderByField']:'id';
        $orderByField = ($orderByField=='name')?$memTable.'.name':$this->boardArticle->table.'.'.$orderByField;
        $orderBySort =  (!empty($params['orderBySort']))?$params['orderBySort']:'desc';
        $this->board->setQueryParams($params);


        $list = $this->boardArticle::orderBy($orderByField,$orderBySort)
                     ->select($this->boardArticle->table.'.*',$memTable.'.name',$memTable.'.nick')
                     ->leftJoin($memTable,$memTable.'.id','=',$this->boardArticle->table.'.user_id')
                     ->where(function($query) {
                         $memTable = config('tables.users');
                         $queryParams = $this->board->queryParams;
                         if(empty($queryParams['getType'])) {
                             $query->where('btype','!=','faq');
                         } else if(!empty($queryParams['getType']) && $queryParams['getType']== 'btype') {
                            if(!empty($queryParams['btype'])) {
                               $query->where('btype','=',$queryParams['btype']);
                            }

                         }
                         if(!empty($queryParams['bid'])) {
                            $query->where('bid','=',$queryParams['bid']);
                         }

                         if(!empty($queryParams['keyword'])) {
                             if($queryParams['keywordCmd']!='subject')$queryParams['keywordCmd'] = $memTable.'.'.$queryParams['keywordCmd'];
                             $query->where($queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                         }

                         if(!empty($queryParams['category'])) {
                             $query->where('category',$queryParams['category']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where($this->boardArticle->table.'.'.$queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($this->boardArticle->table.'.'.$queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                            }
                         }


                     })
                     ->paginate($params['limit']);


        return $list;
    }

    // 게시글 등록
    public function insertArticle(array $fieldsets) {
        $insData = $this->boardArticle::create($fieldsets);
        return $insData;
    }
    // 게시글 수정
    public function updateArticle(int $id,array $params) {

        $updData = $this->boardArticle::find($id)->update($params);
        return ($updData)?$id:'';
    }

    // 게시글삭제
    public function deleteArticle(array $ids) {
        $rows =$this->boardArticle::whereIn('id',$ids)->get();
        foreach($rows as $val) {

            $this->comment::where('parentType','board')->where('parentId',$val->id)->delete();
            $this->boardArticle::destroy($val->id);
        }
        return true;
    }


    // FAQ 게시글 리스트
    public function getFaqList(array $params) {
        $this->board->setQueryParams($params);
        $list = $this->boardArticle::orderBy('id','desc')
                     ->where('btype','faq')
                     ->where(function($query) {
                         $queryParams = $this->board->queryParams;
                         if(!empty($queryParams['keyword'])) {
                             $query->where($queryParams['keywordCmd'],'like','%'.$queryParams['keyword'].'%');
                         }
                         if(!empty($queryParams['category'])) {
                             $query->where('category',$queryParams['category']);
                         }
                         if(!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
                            if(!empty($queryParams['stdate'])) {
                               $query->where($queryParams['dateCmd'],'>=',$queryParams['stdate'].' 00:00:00');
                            }
                            if(!empty($queryParams['endate'])) {
                               $query->where($queryParams['dateCmd'],'<=', $queryParams['endate'].' 23:59:59');
                            }
                         }
                     })
                     ->paginate($params['limit']);


        return $list;
    }

}

