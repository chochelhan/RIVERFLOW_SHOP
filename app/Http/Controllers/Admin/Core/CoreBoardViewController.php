<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeBoardService;

class CoreBoardViewController extends Controller
{
    protected $boardService;

    public function __construct(CustomizeBoardService $boardService) {
        $this->boardService = $boardService;

    }
    /**
    * 게시판 목록
    **/
    public function getBoardList() {
        $list = $this->boardService->getBoardList();
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 게시글 쓰기
    **/
    public function getArticleRegist(Request $request) {
        $list = $this->boardService->getArticleRegist($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 게시글 목록 (검색데이타 가져오기)
    **/
    public function getArticleList(Request $request) {
        $list = $this->boardService->getArticleList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 게시글 목록
    **/
    public function getArticleDataList(Request $request) {
        $list = $this->boardService->getArticleDataList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }


    /**
    * faq 게시글 목록 (검색데이타 가져오기)
    **/
    public function getFaqList(Request $request) {
        $list = $this->boardService->getFaqList($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }
    /**
    * faq 게시글 쓰기
    **/
    public function getFaqRegist(Request $request) {
        $list = $this->boardService->getFaqRegist($request);
        return restResponse(['status'=>'success','data'=>$list]);
    }

}
