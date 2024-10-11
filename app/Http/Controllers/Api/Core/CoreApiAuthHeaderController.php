<?php

namespace App\Http\Controllers\Api\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

use JWTAuth;

/**
*
**/
class CoreApiAuthHeaderController  extends Controller
{
    protected $newToken;
    protected $siteEnv = 'developer';
    protected $csrfTokenError = false;

    public function __construct(Request $request) {

        if(!empty($request->bearerToken())) {
            try {
                JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                // 잘못된 토큰일때
                if ($e instanceof TokenInvalidException) {
                    $this->newToken = ['status'=>'wrong','message'=>'wrongToken'];
                // 토큰이 만료되었을 때
                } elseif ($e instanceof TokenExpiredException) {
                    try {
                        // 토큰 갱신
                        $newToken = Auth('jwt')->setTTL(600)->refresh();
                        JWTAuth::setToken($newToken)->toUser();
                        $this->newToken = ['status'=>'success','token'=>$newToken];

                    } catch (JWTException $e) {
                        $this->newToken = ['status'=>'wrong','message'=>'expiredWrongToken'];
                    }
                } else {
                    $this->newToken = ['status'=>'wrong','message'=>'errorToken'];
                }
            }

            /*
            if(!empty($request->input('authToken')) && $request->input('authToken')=='sdetksiertqdsdwe') {
                $newToken = Auth('jwt')->setTTL(60)->refresh();
                $this->newToken = ['token'=>$newToken,
                                   'expires_in'=> time()+ 3600];

                $user = Auth('jwt')->setToken($newToken)->user();
                $request->session()->put('tempUserInfo',$user);
            }
            */
        }

    }

}
