<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\Customize\CustomizeMemberService;
use App\Services\Admin\Customize\CustomizeMemberLevelService;

use Illuminate\Http\response;

/**
* 괸리자  회원정보
*
**/
class CoreMemberController extends Controller
{
    protected $memberService;
    protected $memberLevelService;

    public function __construct(CustomizeMemberService $memberService,CustomizeMemberLevelService $memberLevelService) {
        $this->memberService = $memberService;
        $this->memberLevelService = $memberLevelService;
    }


	/**
	 * 닉네임 체크
	 **/
	public function checkIsMemberNick(Request $request) {
		$data = $this->memberService->checkIsMemberNick($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}
	/**
	 * 회원정보 수정
	 **/
	public function updateMember(Request $request) {
		$data = $this->memberService->updateMember($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}

	/**
	 * 회원탈퇴 및 삭제
	 **/
	public function updateMemberStatus(Request $request) {
		$data = $this->memberService->updateMemberStatus($request);

		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}
    /**
    * 회원등급 저장
    **/
    public function insertLevel(Request $request) {
        $data = $this->memberLevelService->insertLevel($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /**
    * 회원등급 수정
    **/
    public function updateLevel(Request $request) {
        $data = $this->memberLevelService->updateLevel($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    * 회원등급 삭제
    **/
    public function deleteLevel(Request $request) {
        $data = $this->memberLevelService->deleteLevel($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    * 회원등급 순서변경
    **/
    public function sequenceLevel(Request $request) {
        $data = $this->memberLevelService->sequenceLevel($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    * 회원 적립금 지급/차감
    **/
    public function updateMemberPoint(Request $request) {
        $data = $this->memberService->updateMemberPoint($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    * 회원 쿠폰 지급
    **/
    public function updateMemberCoupon(Request $request) {
        $data = $this->memberService->updateMemberCoupon($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }



}
