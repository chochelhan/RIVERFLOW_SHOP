<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseBoardRepository;

class CoreBoardRepository extends BaseBoardRepository {


    // 정보 리스트
    public function getBoardList() {
        $results = $this->board::orderBy('brank','ASC')->get();
        return $results;
    }
    //faq 게시판 정보
    public function getFaqBoard() {
        return $this->board::where('btype','faq')->first();
    }
    //등록
    public function insertBoard(array $fieldsets) {
        $insData = $this->board::create($fieldsets);
        return $insData;
    }

    // 순위 최대값
    public function getMaxRank() {
        return $this->board::max('brank');
    }
    //수정
    public function updateBoard(int $id,array $params) {

        $updData = $this->board::find($id)->update($params);
        return ($updData)?$id:'';
    }

    //삭제
    public function deleteBoard(int $id) {
        $row =$this->board::find($id);
        $delData = $this->board::destroy($id);
        return ($delData)?$id:'';
    }

    //순서변경
    public function sequenceBoardInfo(array $params) {

        switch($params['cmd']) {
            case 'up':
                $rank = $this->board::where('brank','<',$params['brank'])->max('brank');
            break;
            case 'down':
                $rank = $this->board::where('brank','>',$params['brank'])->min('brank');
            break;
        }
        if(!$rank) {
            return ['rank'=>false];
        }
        $targetRow = $this->board::where('brank',$rank)->first();
        if($targetRow->id) {
            return ['rank'=>$rank,'targetId'=>$targetRow->id];
        } else {
            return ['rank'=>false];
        }

    }

    // 사용가능한 게시판 리스트
    public function getBoardUseList() {
        $results = $this->board::where('buse','yes')->where('btype','!=','faq')->orderBy('brank','ASC')->get();
        return $results;
    }



}

