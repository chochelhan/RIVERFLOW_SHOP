<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseBoardRepository;

class CoreApiBoardRepository extends BaseBoardRepository {


    public function getBoardByType(string $btype) {
        return $this->board::where('btype',$btype)->first();
    }

    // 사용가능한 게시판 리스트
    public function getBoardUseList() {
        $results = $this->board::where('buse','yes')->orderBy('brank','ASC')->get();
        return $results;
    }

}

