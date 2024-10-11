<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeSmsEmailRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;

use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class CoreSmsEmailService
{

	protected $smsEmailRepository;
	protected $siteRepository;

	public function __construct(CustomizeSmsEmailRepository    $smsEmailRepository,
	                            CustomizeSettingSiteRepository $siteRepository)
	{

		$this->smsEmailRepository = $smsEmailRepository;
		$this->siteRepository = $siteRepository;


	}

	/**
	 *@  문자 이메일 등록정보
	 **/
	public function getSmsEmailSetting(Request $request)
	{
		if (!$request->has('gtype')) {
			return ['status' => 'emptyField', 'data' => ''];
		}
		$gtype = $request->input('gtype');
		$data['settingInfo'] = $this->smsEmailRepository->getSmsEmailInfoByGtype($gtype);
		if ($gtype == 'email') {
			foreach ($data['settingInfo'] as $val) {
				if ($val->gid != 'base') $val->emailTemplate = $this->getTemplate($val->gid);
			}
		}
		$data['joinSetting'] = $this->siteRepository->getSiteInfoByField('member');

		return ['status' => 'success', 'data' => $data];
	}

	/**
	 *@  문자 이메일설정정보 수정/저장
	 **/
	public function updateSmsEmailSetting(Request $request)
	{
		if (!$request->has(['gid', 'gtype'])) {
			return ['status' => 'emptyField', 'data' => ''];
		}
		$gid = $request->input('gid');
		$gtype = $request->input('gtype');

		if ($gid == 'base') {
			switch ($gtype) {
				case 'email':
					if (!$request->has(['name', 'email'])) {
						return ['status' => 'emptyField', 'data' => ''];
					}
					$content = [
						'name' => $request->input('name'),
						'email' => $request->input('email'),
					];
					break;
				case 'sms':
					if (!$request->has(['authkey', 'sendPcs'])) {
						return ['status' => 'emptyField', 'data' => ''];
					}
					$content = [
						'smsId' => $request->input('smsId'),
						'authkey' => $request->input('authkey'),
						'sendPcs' => $request->input('sendPcs'),
					];
					break;
			}
			$guse = 'yes';

		} else {
			if (!$request->has(['guse'])) {
				return ['status' => 'emptyField', 'data' => ''];
			}
			$guse = $request->input('guse');
			switch ($gtype) {
				case 'email':
					$content = [
						'subject' => $request->input('subject'),
					];
					break;
				case 'sms':
					$content = [
						'content' => $request->input('content'),
					];
					break;

			}
			if ($gtype == 'email') {
				$this->templateUpdate($gid, $request->input('content'));
			}


		}
		$isInfo = $this->smsEmailRepository->getSmsEmailInfoByGid($gtype, $gid);

		$data = '';
		$params['content'] = json_encode($content);
		$params['gtype'] = $gtype;
		$params['gid'] = $gid;
		$params['guse'] = $guse;

		if ($isInfo && $isInfo->id) {
			$data = $this->smsEmailRepository->updateSmsEmailSetting($isInfo->id, $params);
		} else {
			$data = $this->smsEmailRepository->insertSmsEmailSetting($params);
		}
		if ($data) {
			$status = 'success';
		} else $status = 'error';

		return ['status' => $status, 'data' => $data];
	}

	private function templateUpdate(string $gid, string $content)
	{

		$path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/resources/views/mail/' . $gid . '.blade.php';
		file_put_contents($path, $content);


	}

	private function getTemplate(string $gid)
	{

		$path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/resources/views/mail/' . $gid . '.blade.php';
		if (file_exists($path)) {
			return file_get_contents($path);
		} else return '';
	}

}
