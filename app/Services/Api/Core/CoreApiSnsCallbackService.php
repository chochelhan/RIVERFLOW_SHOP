<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;

class CoreApiSnsCallbackService  {

    protected $memberRepository;

    protected $snsInfo;
    protected $memberConfig;

    public function __construct(CustomizeApiMemberRepository $memberRepository,
                                AuthManager $authManager) {

        $this->authManager = $authManager;
        $this->memberRepository = $memberRepository;

        $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
        $memberConfig = ($siteInfos->member)?json_decode($siteInfos->member):'';
        $this->memberConfig = $memberConfig;
        $this->snsInfo = $memberConfig->snsInfo;
        if(!empty($this->snsInfo)) {
            if(!empty($this->snsInfo->ka)) {
                $this->snsInfo->ka->redirectUrl = str_replace('admin.','',$this->snsInfo->ka->redirectUrl);
            }
            if(!empty($this->snsInfo->nv)) {
                $this->snsInfo->nv->redirectUrl = str_replace('admin.','',$this->snsInfo->nv->redirectUrl);
            }
            if(!empty($this->snsInfo->fb)) {
                $this->snsInfo->fb->redirectUrl = str_replace('admin.','',$this->snsInfo->fb->redirectUrl);
            }

        }

    }

    // 카카오 로그인
    public function kakaoLogin(Request $request) {

        $apiKey = $this->snsInfo->ka->key;
        $redirectURI  = $this->snsInfo->ka->redirectUrl;
        $params['url'] = "https://kauth.kakao.com/oauth/token";
        $params['post_data'] = http_build_query([
            'code' => $request->input('code')
            , 'grant_type' => 'authorization_code'
            , 'client_id' => $apiKey
            , 'redirect_uri' => $redirectURI
        ]);
        $tokenResult = postCurl($params);
        if($tokenResult['status'] == 'success') {
            $tokenInfo = json_decode($tokenResult['data']);
            if($tokenInfo->access_token) {
                $accessToken = $tokenInfo->access_token;

                $tokenInfoParams['url'] = "https://kapi.kakao.com/v2/user/me";
                $tokenInfoParams['headers'] = [
                    'Authorization: Bearer '.$accessToken
                ];
                $userInfoResult = getCurl($tokenInfoParams);
                if($userInfoResult['status'] == 'success') {
                    $dataStrings = explode('{',$userInfoResult['data']);
                    if(!empty($dataStrings[1])) {
                        $jsonString = str_replace($dataStrings[0],'',$userInfoResult['data']);
                    } else {
                        $jsonString = $userInfoResult['data'];
                    }
                    $userInfo = json_decode($jsonString);
                    $userId = 'ka'.$userInfo->id;
                    $userName = (!empty($userInfo->kakao_account->profile->nickname))?$userInfo->kakao_account->profile->nickname:'';
                    $userImg =  (!empty($userInfo->kakao_account->profile->profile_image_url))?$userInfo->kakao_account->profile->profile_image_url:'';

                    $isUserInfo = $this->memberRepository->checkUid($userId);

                    if(!empty($isUserInfo)) { // 회원가입 된 경우
                        // 로그인 시킨다
                        $guard =  $this->authManager->guard('jwt');
                        $credentials = [
                            'uid'=>$userId,
                            'password'=>$userId,
                            'admin'=>'no',
                        ];
                        $token = $guard->setTTL(600)->attempt($credentials);
                        if($token) {
                            $data['tokenInfo'] = ['token' => $token,
                                                 'token_type' => 'bearer'];
                            $data['userInfo'] = $guard->user();
                            $status = 'login';
                        } else {
                            $data = '';
                            $status = 'message';
                        }
                        return ['status'=>$status,'data'=>$data];
                    } else {
                        $tempSnsInfos['sns'] = 'ka';
                        $tempSnsInfos['uid'] = $userId;
                        $tempSnsInfos['upass'] = $userId;
                        $tempSnsInfos['name'] = $userName;
                        $tempSnsInfos['img'] = $userImg;
                        $joinStatus = $this->snsJoinCheck($tempSnsInfos);
                        return ['status'=>$joinStatus,'data'=>$tempSnsInfos];
                    }


                }  return false;
            } else return false;
        } else return false;
    }

    // 네이버 로그인
    public function naverLogin(Request $request) {

        $apiKey = $this->snsInfo->nv->key;
        $apiSecret = $this->snsInfo->nv->secret;
        $redirectURI  = $this->snsInfo->nv->redirectUrl;
        $code = $request->input('code');
        $params['url'] = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$apiKey."&client_secret=".$apiSecret."&redirect_uri=".$redirectURI."&code=".$code;
        $tokenResult = getCurl($params);
        if($tokenResult['status'] == 'success') {
            $tokenInfo = json_decode($tokenResult['data']);
            if($tokenInfo->access_token) {
                $accessToken = $tokenInfo->access_token;

                $tokenInfoParams['url'] = "https://openapi.naver.com/v1/nid/me";
                $tokenInfoParams['headers'] = [
                    'Authorization: Bearer '.$accessToken
                ];
                $userInfoResult = getCurl($tokenInfoParams);

                if($userInfoResult['status'] == 'success') {
                    $dataStrings = explode('{',$userInfoResult['data']);
                    if(!empty($dataStrings[1])) {
                        $jsonString = str_replace($dataStrings[0],'',$userInfoResult['data']);
                    } else {
                        $jsonString = $userInfoResult['data'];
                    }
                    $naverUserInfo = json_decode($jsonString);
                    $userInfo = $naverUserInfo->response;

                    $userId = 'nv'.$userInfo->id;
                    $userName = (!empty($userInfo->name))?$userInfo->name:'';
                    $userImg =  (!empty($userInfo->profile_image))?$userInfo->profile_image:'';
                    $isUserInfo = $this->memberRepository->checkUid($userId);

                    if(!empty($isUserInfo)) { // 회원가입 된 경우
                        // 로그인 시킨다
                        $guard =  $this->authManager->guard('jwt');
                        $credentials = [
                            'uid'=>$userId,
                            'password'=>$userId,
                            'admin'=>'no',
                        ];
                        $token = $guard->setTTL(600)->attempt($credentials);
                        if($token) {
                            $data['tokenInfo'] = ['token' => $token,
                                                 'token_type' => 'bearer'];
                            $data['userInfo'] = $guard->user();
                            $status = 'login';
                        } else {
                            $data = '';
                            $status = 'message';
                        }
                        return ['status'=>$status,'data'=>$data];
                    } else {
                        $tempSnsInfos['sns'] = 'nv';
                        $tempSnsInfos['uid'] = $userId;
                        $tempSnsInfos['upass'] = $userId;
                        $tempSnsInfos['name'] = $userName;
                        $tempSnsInfos['img'] = $userImg;
                        $joinStatus = $this->snsJoinCheck($tempSnsInfos);
                        return ['status'=>$joinStatus,'data'=>$tempSnsInfos];
                    }


                }  return false;
            } else return false;
        } else return false;
    }

    // 페이스북 로그인
    public function facebookLogin(Request $request) {

        $apiKey = $this->snsInfo->fb->key;
        $redirectURI  = $this->snsInfo->fb->redirectUrl;
        $apiSecret = $this->snsInfo->fb->secret;
         $code = $request->input('code');
        $params['url'] = "https://graph.facebook.com/v4.0/oauth/access_token?client_id=".$apiKey."&client_secret=".$apiSecret."&redirect_uri=".$redirectURI."&code=".$code;
        $tokenResult = getCurl($params);
        if($tokenResult['status'] == 'success') {
            $tokenInfo = json_decode($tokenResult['data']);
            if($tokenInfo->access_token) {
                $accessToken = $tokenInfo->access_token;

                $tokenInfoParams['url'] = "https://graph.facebook.com/v4.0/me?fields=id,name";
                $tokenInfoParams['headers'] = [
                    'Authorization: Bearer '.$accessToken
                ];
                $userInfoResult = getCurl($tokenInfoParams);
                if($userInfoResult['status'] == 'success') {
                    $dataStrings = explode('{',$userInfoResult['data']);
                    if(!empty($dataStrings[2])) {
                        $jsonString = str_replace($dataStrings[0],'',$userInfoResult['data']);
                        $jsonString = str_replace($dataStrings[1],'',$jsonString);
                        $jsonString = str_replace('{{','{',$jsonString);

                    } else if(!empty($dataStrings[1])) {
                        $jsonString = str_replace($dataStrings[0],'',$userInfoResult['data']);
                    } else {
                        $jsonString = $userInfoResult['data'];
                    }
                    $userInfo = json_decode($jsonString);
                    $userId = 'fb'.$userInfo->id;
                    $userName = (!empty($userInfo->name))?$userInfo->name:'';
                    $userImg =  (!empty($userInfo->profile_pic))?$userInfo->profile_pic:'';

                    $isUserInfo = $this->memberRepository->checkUid($userId);

                    if(!empty($isUserInfo)) { // 회원가입 된 경우
                        // 로그인 시킨다
                        $guard =  $this->authManager->guard('jwt');
                        $credentials = [
                            'uid'=>$userId,
                            'password'=>$userId,
                            'admin'=>'no',
                        ];
                        $token = $guard->setTTL(600)->attempt($credentials);
                        if($token) {
                            $data['tokenInfo'] = ['token' => $token,
                                                 'token_type' => 'bearer'];
                            $data['userInfo'] = $guard->user();
                            $status = 'login';
                        } else {
                            $data = '';
                            $status = 'message';
                        }
                        return ['status'=>$status,'data'=>$data];
                    } else {
                        $tempSnsInfos['sns'] = 'fb';
                        $tempSnsInfos['uid'] = $userId;
                        $tempSnsInfos['upass'] = $userId;
                        $tempSnsInfos['name'] = $userName;
                        $tempSnsInfos['img'] = $userImg;
                        $joinStatus = $this->snsJoinCheck($tempSnsInfos);
                        return ['status'=>$joinStatus,'data'=>$tempSnsInfos];
                    }

                }  return false;

            } else return false;
        } else return false;
    }

    private function snsJoinCheck($params) {
        $nickRequired = false;
        if($this->memberConfig->nickUse == 'yes' && $this->memberConfig->nickRequired == 'yes') {
            $nickRequired = true;
        }
        $pcsRequired = false;
        if($this->memberConfig->pcsUse == 'yes' && $this->memberConfig->pcsRequired == 'yes') {
            $pcsRequired = true;
        }
        if(empty($params['name']) || $pcsRequired || $nickRequired) {
            return 'required';   // 더 많은 정보를 필요로 할경우 리턴시킨다
        } else {
            return 'agree';   // 회원가입 동의 항목으로 이동
        }

    }
}
