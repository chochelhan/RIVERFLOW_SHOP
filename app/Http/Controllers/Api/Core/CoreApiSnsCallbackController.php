<?php

namespace App\Http\Controllers\Api\Core;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Api\Customize\CustomizeApiSnsCallbackService;
use Illuminate\Support\Facades\View;
/**
* 회원
*
**/
class CoreApiSnsCallbackController extends Controller
{

    protected $snsService;

    public function __construct(CustomizeApiSnsCallbackService $snsService) {
        $this->snsService = $snsService;

    }

    // 카카오 로그인
    public function kakaoLogin(Request $request) {
        $data = $this->snsService->kakaoLogin($request);
        if(empty($data)) {
           $data['status'] = 'error';
        }
        return view('snsLogin.script',['data'=>$data]);
    }
    // 페이스북 로그인
    public function facebookLogin(Request $request) {
       $data = $this->snsService->facebookLogin($request);
        return view('snsLogin.script',['data'=>$data]);


    }
    // 네이버 로그인
    public function naverLogin(Request $request) {
       $data = $this->snsService->naverLogin($request);
        if(empty($data)) {
           $data['status'] = 'error';
        }
        return view('snsLogin.script',['data'=>$data]);


    }
}
