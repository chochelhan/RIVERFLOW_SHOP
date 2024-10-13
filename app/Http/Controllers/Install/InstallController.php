<?php

namespace App\Http\Controllers\Install;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\response;

class InstallController extends Controller
{

	public function install(Request $request)
	{

		if (!$request->has(['dbHost', 'dbPort', 'dbId', 'dbPw', 'dbName'])) {
			return $this->restResponse(['status' => 'message', 'data' => 'fieldError']);
		}

		$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$dbJson = $path . '/install/db.json';
		if (!file_exists($dbJson)) {
			$dbPrams['dbHost'] = $request->input('dbHost');
			$dbPrams['dbPort'] = $request->input('dbPort');
			$dbPrams['dbName'] = $request->input('dbName');
			$dbPrams['dbUserName'] = $request->input('dbId');
			$dbPrams['dbPassword'] = $request->input('dbPw');

			$dbfile = fopen($dbJson, "a") or die("Unable to open file!");
			$txt = json_encode($dbPrams);
			fwrite($dbfile, $txt);
			fclose($dbfile);
			return $this->restResponse(['status' => 'success', 'data' => '']);
		} else {
			return $this->restResponse(['status' => 'message', 'data' => '']);
		}

	}

	public function getFile()
	{

		$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$dbJson = $path . '/install/db.json';
		if (!file_exists($dbJson)) {
			return $this->restResponse(['status' => 'success', 'data' => '']);
		} else {
			return $this->checkDbAndPermission('check');
		}

	}

	public function checkDb()
	{
		return $this->checkDbAndPermission('change');

	}
	public function checkDbAndPermission(string $permission)
	{

		try {
			DB::connection()->getPdo();
			$tableList = DB::select('SHOW TABLES');
			if(count($tableList)>0) {
				if($permission=='change') {
					return $this->restResponse(['status' => 'message', 'data' => 'complete']);
				} else {
					return $this->restResponse(['status' => 'message', 'data' => 'already']);
				}
			} else {
				if($permission=='change') {
					return $this->restResponse(['status' => 'success', 'data' => '']);
				} else {
					return $this->restResponse(['status' => 'message', 'data' => 'noTable']);
				}
			}
		} catch (\Exception $e) {
			$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$dbJson = $path . '/install/db.json';
			if (file_exists($dbJson)) {
				unlink($dbJson);
			}
			return $this->restResponse(['status' => 'message', 'data' => 'wrong']);
		}

	}
	public function makeTableAndInsertData(Request $request)
	{

		if (!$request->has(['adminName', 'adminId', 'adminPw'])) {
			return $this->restResponse(['status' => 'message', 'data' => 'fieldError']);

		}
		$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$dbJson = $path . '/install/db.json';
		if (!file_exists($dbJson)) {
			return $this->restResponse(['status' => 'message', 'data' => 'fileError']);
		}
		try {

			Artisan::call('migrate');

			$memberTable = config('tables.users');
			$adminPass = Hash::make($request->input('adminPw'));
			$memberParams = ['uid' => $request->input('adminId'),
				'name' => $request->input('adminName'),
				'email' => 'admin@sample.com',
				'email_verified_at' => now(),
				'admin' => 'yes',
				'password' => $adminPass, // password
				'remember_token' => Str::random(10)];
			DB::table($memberTable)->insert($memberParams);

			$memberLevelTable = config('tables.memberLevel');
			$memberLevelParams = [
				'gname' => '기본등급',///등급명
				'guse' => 'yes',  //사용여부
				'gprice' => 0, // 승급 기준 구매금액
				'gbase' => 'yes', // 기본 등급 여부
				'gservicePointUse' => 'no', // 승급시 적립금 지급여부
				'gservicePoint' => 0, // 승급시 적립금
				'gpointUse' => 'no', // 구매시 적립금 적립 여부
				'gpoint' => 0, // 구매시 적립금 적립 %
				'grank' => 1 // 등급
			];
			DB::table($memberLevelTable)->insert($memberLevelParams);

			$boardTable = config('tables.board');
			$boardParams = [
				'bname' => 'FAQ',
				'buse' => 'yes',
				'categoryUse' => 'yes',
				'categoryList' => '[{"code": "b3_1", "name": "배송"}, {"code": "b3_2", "name": "환불"}]',
				'wauth' => 'admin',
				'secret' => 'no',
				'btype' => 'faq',
				'replyUse' => 'no',
				'rauth' => 'no',
				'brank' => 1
			];
			DB::table($boardTable)->insert($boardParams);
			$boardParams = [
				'bname' => '공지사항',
				'buse' => 'yes',
				'categoryUse' => 'no',
				'categoryList' => '[]',
				'wauth' => 'admin',
				'secret' => 'no',
				'btype' => 'notice',
				'replyUse' => 'no',
				'rauth' => 'no',
				'brank' => 2
			];
			DB::table($boardTable)->insert($boardParams);

			$settingTable = config('tables.settingSite');
			$settingParams = [
				'images'=>'{"blist": {"width": "250", "height": "250"}, "plist": {"width": "276", "height": "270"}, "prlist": {"width": "200", "height": "150"}, "pdetail": {"width": 640, "height": 640}}',
                'agrees'=>'{"out": {"use": "no", "content": ""}, "use": {"use": "no", "content": ""}, "collect": {"use": "no", "content": ""}, "privacy": {"use": "no","content":""}}',
			    'siteEnv'=>'{"siteEnv": "developer"}'
			];
			DB::table($settingTable)->insert($settingParams);


		} catch (\Exception $e) {
			if (file_exists($dbJson)) {
				unlink($dbJson);
			}
			return $this->restResponse(['status' => 'message', 'data' => 'tableError']);
		}
		try {
			$this->changeDirPermission();
		} catch (\Exception $e) {
			return $this->restResponse(['status' => 'message', 'data' => 'dirChangeError']);
		}
		return $this->restResponse(['status' => 'success', 'data' => '']);

	}

	private function changeDirPermission()
	{
		$path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
		$directory = $path . '/storage';


		$boardPath = $directory . '/app/public/board';
		if (!is_dir($boardPath)) {
			mkdir($boardPath);
			chmod($boardPath, 0777);

		}
		$boardSymPath = $path . '/public/boardImages';
		symlink($boardPath, $boardSymPath);

		$productPath = $directory . '/app/public/product';
		if (!is_dir($productPath)) {
			mkdir($productPath);
			chmod($productPath, 0777);
		}
		$productSymPath = $path . '/public/productImages';
		symlink($productPath, $productSymPath);


		$productDetailPath = $directory . '/app/public/productDetail';
		if (!is_dir($productDetailPath)) {
			mkdir($productDetailPath);
			chmod($productDetailPath, 0777);
		}
		$productDetailSymPath = $path . '/public/productDetailImages';
		symlink($productDetailPath, $productDetailSymPath);


		$bannerPath = $directory . '/app/public/banner';
		if (!is_dir($bannerPath)) {
			mkdir($bannerPath);
			chmod($bannerPath, 0777);
		}
		$bannerSymPath = $path . '/public/bannerImages';
		symlink($bannerPath, $bannerSymPath);


	}

	private function restResponse($data)
	{
		return response()->json(['status' => $data['status'], 'data' => $data['data']]);
	}
}
