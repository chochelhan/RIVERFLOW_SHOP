<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeCommentService;
use Illuminate\Http\response;


class CoreCommentViewController extends Controller
{
    protected $commentService;
    public function __construct(CustomizeCommentService $commentService) {
        $this->commentService = $commentService;

    }

    /*** 댓글 목록 **/
    public function getCommentList(Request $request) {
        $data = $this->commentService->getCommentList($request);

	    return response()->json(['status' => 'success','data'=>$data]);
    }



}
