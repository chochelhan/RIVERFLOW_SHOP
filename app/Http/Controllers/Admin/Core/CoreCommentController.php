<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeCommentService;
use Illuminate\Http\response;

class CoreCommentController extends Controller
{
    protected $commentService;
    public function __construct(CustomizeCommentService $commentService) {
        $this->commentService = $commentService;

    }

    /*** 댓글 저장 **/
    public function insertComment(Request $request) {
        $data = $this->commentService->insertComment($request);
	    return response()->json(['status' => $data['status'], 'data' => $data['data']]);;
    }

    /*** 댓글  수정 **/
    public function updateComment(Request $request) {
        $data = $this->commentService->updateComment($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);;

    }

    /*** 댓글  블라인드 처리 **/
    public function blindComment(Request $request) {
        $data = $this->commentService->blindComment($request);
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);;

    }

}
