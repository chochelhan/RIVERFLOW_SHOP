<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeMemberService;
use App\Services\Admin\Customize\CustomizeMemberLevelService;
use App\Services\Admin\Customize\CustomizeSettingSiteService;


/**
 * 괸리자 로그인 및 회원정보
 *
 **/
class CoreMemberViewController extends Controller
{
	protected $memberService;
	protected $memberLevelService;
	protected $settingService;

	public function __construct(CustomizeMemberService $memberService, CustomizeMemberLevelService $memberLevelService, CustomizeSettingSiteService $settingService)
	{
		$this->memberService = $memberService;
		$this->memberLevelService = $memberLevelService;
		$this->settingService = $settingService;
	}

	// 회원목록
	public function getAllList(Request $request)
	{
		$data['list'] = $this->memberService->getMemberList($request);
		$settingInfo = $this->settingService->getMemberInfo();
		$data['memberSetting'] = $settingInfo['data'];
		return response()->json(['status' => 'success', 'data' =>$data]);

	}

	// 회원목록(쿠폰목록 포함)
	public function getMemberListByCoupon(Request $request)
	{
		$list = $this->memberService->getMemberListByCoupon($request);

		return response()->json(['status' => 'success', 'data' =>$list]);

	}

	// 회원정보
	public function getMemberInfoById(Request $request)
	{

		$data['memberInfo'] = $this->memberService->getMemberInfo((int)$request->input('id'));
		$settingInfo = $this->settingService->getMemberInfo();
		$data['memberSetting'] = $settingInfo['data'];
		return response()->json(['status' => 'success', 'data' =>$data]);

	}

	// 회원목록 (데이타만)
	public function getMemberDataList(Request $request)
	{
		$list = $this->memberService->getMemberDataList($request);

		return response()->json(['status' => 'success', 'data' =>$list]);

	}

	// 회원레벨 목록
	public function getLevelList()
	{
		$list = $this->memberLevelService->getLevelList();

		return response()->json(['status' => 'success', 'data' =>$list]);
	}

	// 적립금 내역
	public function getPointList(Request $request)
	{
		$list = $this->memberService->getMemberPointList($request);
		return response()->json(['status' => 'success', 'data' =>$list]);

	}


}
