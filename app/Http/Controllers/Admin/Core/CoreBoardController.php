<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeBoardService;
use Illuminate\Http\response;

class CoreBoardController extends Controller
{
    protected $boardService;
    public function __construct(CustomizeBoardService $boardService) {
        $this->boardService = $boardService;

    }

    /***  게시판 저장 **/
    public function insertBoard(Request $request) {
        $data = $this->boardService->insertBoard($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /*** 게시판  수정 **/
    public function updateBoard(Request $request) {
        $data = $this->boardService->updateBoard($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 게시판  삭제 **/
    public function deleteBoard(Request $request) {
        $data = $this->boardService->deleteBoard($request);
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 게시판 순서 변경 **/
    public function sequenceBoard(Request $request) {
        $data = $this->boardService->sequenceBoard($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /***  게시글 저장 **/
    public function insertArticle(Request $request) {
        $data = $this->boardService->insertArticle($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /*** 게시글  수정 **/
    public function updateArticle(Request $request) {
        $data = $this->boardService->updateArticle($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }
    /*** 게시글  삭제 **/
    public function deleteArticle(Request $request) {
        $data = $this->boardService->deleteArticle($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    public function insertArticleTempImage(Request $request) {
        $data = $this->boardService->insertArticleTempImage($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

}
