<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeMemberRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeMemberLevelRepository;
use App\Repositories\Repository\Admin\Customize\CustomizePointRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeCouponRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CoreMemberService
{

	protected $memberRepository;
	protected $memberLevelRepository;
	protected $pointRepository;
	protected $couponRepository;

	public function __construct(CustomizeMemberRepository      $memberRepository,
	                            CustomizeMemberLevelRepository $memberLevelRepository,
	                            CustomizePointRepository       $pointRepository,
	                            CustomizeCouponRepository      $couponRepository)
	{

		$this->memberRepository = $memberRepository;
		$this->memberLevelRepository = $memberLevelRepository;
		$this->pointRepository = $pointRepository;
		$this->couponRepository = $couponRepository;
	}

	public function getMemberInfo(int $id)
	{

		return $this->memberRepository->getMemberById($id);
	}

	// 닉네임 중복체크
	public function checkIsMemberNick(Request $params)
	{
		$requestParams = $params->all();
		$memberInfo = $this->memberRepository->checkIsMemberNick((int)$requestParams['id'], $requestParams['nick']);
		if ($memberInfo) {
			$data = 'disable';
		} else {
			$data = 'able';
		}
		return ['status' => 'success', 'data' => $data];
	}

	public function updateMember(Request $request)
	{
		$updateData['pcs'] = (!empty($request->input('pcs'))) ? $request->input('pcs') : '';
		if (!empty($request->input('upass')) && !empty($request->input('nowupass'))) {
			$isMember = $this->memberRepository->getMemberById((int)$request->input('id'));
			if (Hash::check($request->input('nowupass'), $isMember['password'])) {
				$updateData['password'] = Hash::make($request->input('upass'));
			} else {
				return ['status' => 'message', 'data' => 'wrongNowpass'];
			}
		}
		$updateData['name'] = $request->input('name');
		if (!empty($request->input('nick'))) {
			$updateData['nick'] = $request->input('nick');
		}
		$member = $this->memberRepository->updateMember((int)$request->input('id'), $updateData);
		if (!empty($member)) {
			$status = 'success';
			$data = '';
		} else {
			$status = 'error';
			$data = '';
		}
		return ['status' => $status, 'data' => $data];
	}


	public function updateMemberStatus(Request $request)
	{
		$ids = $request->input('ids');
		$mstatus = $request->input('mstatus');
		foreach ($ids as $id) {
			$updateData['name'] = '탈퇴';
			$updateData['nick'] = '탈퇴';
			$updateData['point'] = 0;
			//$updateData['uid'] = '';
			$updateData['email'] = '';
			$updateData['pcs'] = '';
			$updateData['sns'] = '';
			$updateData['img'] = '';
			$updateData['password'] = '';
			if ($mstatus == 'out') {
				$updateData['mstatus'] = 'end';

			} else {
				$updateData['mstatus'] = 'out';
			}
			$member = $this->memberRepository->updateMember((int)$id, $updateData);

		}
		$status = 'success';
		return ['status' => $status, 'data' => ''];
	}

	public function getMemberList(Request $params)
	{
		$requestParams = $params->all();

		$list['levelList'] = $this->memberLevelRepository->getLevelUseList();
		$list['memberList'] = $this->memberRepository->getMemberList($requestParams);
		return $list;
	}

	// 회원목록(쿠폰목록 포함)
	public function getMemberListByCoupon(Request $params)
	{
		$requestParams = $params->all();

		$list['couponList'] = $this->couponRepository->getCouponUseList();
		$list['levelList'] = $this->memberLevelRepository->getLevelUseList();
		$list['memberList'] = $this->memberRepository->getMemberList($requestParams);
		return $list;
	}

	// 회원목록(데이타만)
	public function getMemberDataList(Request $params)
	{
		$requestParams = $params->all();
		$list = $this->memberRepository->getMemberList($requestParams);
		return $list;
	}

	// 포인트 내역
	public function getMemberPointList(Request $params)
	{
		$requestParams = $params->all();
		$list['pcodeList'] = $this->pointRepository->PCODES;
		$list['pointList'] = $this->pointRepository->getPointList($requestParams);
		return $list;
	}

	// 포인트 지급/차감
	public function updateMemberPoint(Request $params)
	{

		$requestParams = $params->all();
		if (empty($requestParams['pointMsg'])) {
			$requestParams['pointMsg'] = $this->pointRepository->PCODES[$params->input('pcode')];
		}
		foreach ($params->input('ids') as $user_id) {
			$requestParams['user_id'] = $user_id;
			$pointFieldSet = makeFieldset($this->pointRepository->useFields, $requestParams);
			$this->pointRepository->insertPoint($pointFieldSet);
			$pt = $this->memberRepository->updateMemberPoint($pointFieldSet);
		}
		return ['status' => 'success', 'data' => $pt];
	}

	// 쿠폰 지급
	public function updateMemberCoupon(Request $params)
	{

		$couponInfo = $this->couponRepository->getCouponInfo($params->input('coupon'));
		if (!$couponInfo->id) return ['status' => 'fail', 'data' => ''];

		$publishTotal = $this->couponRepository->getPublishTotal($couponInfo->id);
		if ($publishTotal >= $couponInfo->camt) return ['status' => 'message', 'data' => 'moreCamt'];

		if (empty($params->input('couponMsg'))) {
			$insertData['couponMsg'] = $this->couponRepository->GTYPES['direct'];
		} else {
			$insertData['couponMsg'] = $params->input('couponMsg');
		}

		if ($couponInfo->useExpireType == 'after') { //사용가능기간 구분 after(발급일로 부터 ), date(특정일 지정)

			$insertData['expireStdate'] = date('Y-m-d');
			$insertData['expireEndate'] = date('Y-m-d', mktime(1, 1, 1, date('m'), date('d') + $couponInfo->afterDay, date('Y')));
		} else {
			$insertData['expireStdate'] = $couponInfo->expireStdate;
			$insertData['expireEndate'] = $couponInfo->expireEndate;
		}
		$insertData['ctype'] = $couponInfo->ctype;
		$insertData['cname'] = $couponInfo->cname;
		$insertData['pubtype'] = $couponInfo->pubtype;

		$insertData['gtype'] = 'direct';
		$insertData['discountType'] = $couponInfo->discountType;
		$insertData['discountPrice'] = $couponInfo->discountPrice;
		$insertData['discountRate'] = $couponInfo->discountRate;
		$insertData['discountRatePrice'] = $couponInfo->discountRatePrice;
		$insertData['cid'] = $couponInfo->id;

		$camtTotal = $publishTotal;
		$camtMore = false;
		foreach ($params->input('ids') as $user_id) {
			$camtTotal++; // 발행수량 체크
			if ($camtTotal >= $couponInfo->camt) {
				$camtMore = true;
			} else {
				$insertData['user_id'] = $user_id;
				$couponFieldSet = makeFieldset($this->couponRepository->publishUseFields, $insertData);
				switch ($couponInfo->pubtype) {
					case 'code': // 난수코드 발급
						break;
					case 'direct'://관리자 직접발급
						$this->couponRepository->insertCouponPublish($couponFieldSet);
						break;
				}
			}

		}
		if ($camtMore) {
			return ['status' => 'message', 'data' => 'moreCamt'];
		} else {
			return ['status' => 'success', 'data' => ''];
		}

	}

	// 관리자 정보 변경
	public function updateAdminInfo(int $id, array $params)
	{
		$data = $this->memberRepository->updateMember($id, $params);
		return ['status' => 'success', 'data' => $data];
	}
}
