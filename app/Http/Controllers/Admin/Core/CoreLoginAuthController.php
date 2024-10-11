<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Admin\Customize\CustomizeSettingSiteService;

use Illuminate\Support\Facades\Auth;
use function response;

/**
 * 괸리자 로그인
 *
 **/
class CoreLoginAuthController extends Controller
{

    protected $siteService;

    public function __construct(CustomizeSettingSiteService $siteService)
    {
        $this->siteService = $siteService;

    }

    public function checkLogin(Request $request)
    {
        $siteEnv = $this->siteService->getSiteEnv();
        $token = '';
        //if ($siteEnv == 'developer') {
            $token = csrf_token();
        //}
        $data = Auth::user();

        return response()->json(['status' => 'success', 'token' => $token, 'data' => $data, 'siteEnv' => $siteEnv]);
    }

    public function login(Request $request)
    {

        $params = $request->only('adminId', 'adminPw');
        $uid = $params['adminId'];
        $password = $params['adminPw'];
        $credentials = [
            'uid' => $uid,
            'password' => $password,
            'admin' => 'yes',
        ];
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $status = 'success';
            $data = $request->session()->all();


            $response = response()->json(['status' => $status, 'data' => $data]);
        } else {
            $response = response()->json(['status' => 'message']);
        }

        return $response;

    }

    public function logout(Request $request)
    {


        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = response()->json(['status' => 'success']);
        return $response;

    }

}
