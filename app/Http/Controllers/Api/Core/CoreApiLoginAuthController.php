<?php

namespace App\Http\Controllers\Api\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
//use Illuminate\Support\Facades\Auth;
/**
* 회원 로그인
*
**/
class CoreApiLoginAuthController extends Controller
{

    private $authManager;
    public function __construct(AuthManager $authManager) {
        $this->authManager = $authManager;
    }


    public function login(Request $request) {

         $guard =  $this->authManager->guard('jwt');
         $params = $request->only('uid', 'pw');
         $uid = $params['uid'];
         $password = $params['pw'];
         $credentials = [
            'uid'=>$uid,
            'password'=>$password,
            'admin'=>'no',
         ];
         $token = $guard->setTTL(600)->attempt($credentials);
         if($token) {
            $data['tokenInfo'] = ['token' => $token,
                               'token_type' => 'bearer'];
            $data['userInfo'] = $guard->user();
            $status = 'success';




         } else {
            $data = '';
            $status = 'message';
         }

         return restResponse(['status'=>$status,'data'=>$data]);

    }

    public function logout(Request $request) {


        $guard =  $this->authManager->guard('jwt');
        $guard->logout();
        $request->session()->invalidate();
        $response = response()->json(['status'=>'success']);
        return $response;

    }

}
