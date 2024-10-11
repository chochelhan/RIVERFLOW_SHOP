<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiMemberService;

/**
* 회원
*
**/
class CoreApiMemberController extends CoreApiAuthHeaderController
{

    protected $memberService;

    public function __construct(Request $request,CustomizeApiMemberService $memberService) {
        parent::__construct($request);
        $this->memberService = $memberService;

    }

    public function join(Request $request) {

        $data = $this->memberService->join($request);
        return restResponse($data);
    }
    /**
    *
    *@ 아아디 중복체크
    **/
    public function checkUid(Request $request) {
        if(empty($request->has('uid'))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->checkUid($request->input('uid'));
        return restResponse($data);
    }
    /**
    *
    *@  닉네임 중복체크
    **/
    public function checkNick(Request $request) {
        if(empty($request->has('nick'))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->checkNick($request->input('nick'));
        return restResponse($data);
    }
    /**
    *
    *@ 회원가입 인증메일 보내기
    **/
    public function sendAuthEmail(Request $request) {
        if(empty($request->has('email'))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->sendAuthEmail($request);
        return restResponse($data);
    }

    /**
    *
    *@ 회원가입 인증번호 문자 보내기
    **/
    public function sendAuthPcs(Request $request) {
        if(empty($request->has('pcs'))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->sendAuthPcs($request);
        return restResponse($data);
    }

    /**
    *
    *@ 회원가입 인증번호 확인
    **/
    public function getAuthNumberConfirm(Request $request) {
        if(empty($request->has('authNumber'))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->confirmAuthNumber($request);
        return restResponse($data);
    }

    /**
    *
    *@ 비밀번호 찾기
    **/
    public function findMemberUpass(Request $request) {
        if(empty($request->has(['name','email']))) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }
        $data = $this->memberService->findMemberUpass($request);
        return restResponse($data);
    }



}
