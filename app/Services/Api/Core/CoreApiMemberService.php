<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiMemberLevelRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiPointRepository;

use App\Events\MailEvent;
use App\Events\SmsEvent;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use App\Sms\SendSms;



class CoreApiMemberService extends CoreApiAuthHeader {

    protected $memberRepository;
    protected $memberLevelRepository;
    protected $pointRepository;

    protected $config;
    protected $agree;
    protected $pointConfig;
    protected $authManager;

    public function __construct(Request $request,CustomizeApiMemberRepository $memberRepository,
                                CustomizeApiMemberLevelRepository $memberLevelRepository,
                                AuthManager $authManager,CustomizeApiPointRepository $pointRepository) {
        parent::__construct($request);

        $this->authManager = $authManager;
        $this->memberRepository = $memberRepository;
        $this->memberLevelRepository = $memberLevelRepository;
        $this->pointRepository = $pointRepository;

        $this->config = ($this->siteInfos['member'])?$this->siteInfos['member']:'';
        $this->pointConfig = ($this->siteInfos['points'])?$this->siteInfos['points']:'';
        $this->agree = ($this->siteInfos['agrees'])?$this->siteInfos['agrees']:'';

        //$this->isLoginInfo;
    }

    // 회원가입
    public function join(Request $request) {
        if(!empty($this->isLoginInfo)) {
            return ['status'=>'error','data'=>''];
        }
        
        $requiredFields = ['name', 'uid','upass'];
        if($this->config && $this->config->pcsUse=='yes' && $this->config->pcsRequired=='yes') {
                $requiredFields[] = 'pcs';

        }
        if($this->config && $this->config->nickUse=='yes' && $this->config->nickRequired=='yes') {
            $requiredFields[] = 'nick';

        }
        if (!$request->has($requiredFields)) {
                return ['status'=>'error','data'=>'required'];
        }
        /// 아이디 중복 체크
        $doubleUid = $this->memberRepository->checkUid($request->input('uid'));
        if($doubleUid) {
             return ['status'=>'message','data'=>'doubleUid'];
        }
        if(empty($request->input('joinType'))) {
            if($this->config && $this->config->authType!='no') {
                $authNumber = $request->input('authNumber');
                if($request->session()->get('authNumber') != $authNumber) {
                    return ['status'=>'message','data'=>'wrongAuthNumber'];
                }
            }
        } else {
            if(!empty($request->input('img'))) {
                $insertData['img'] = $request->input('img');
            }
            $insertData['sns'] = $request->input('sns');
        }
        $baseLevelInfo = $this->memberLevelRepository->getBaseLevelInfo(); // 기본등급 정보
        $insertData['lvid'] = $baseLevelInfo->id;
        if($this->pointConfig && $this->pointConfig->joinPointUse == 'yes') { // 회원가입 포인트 지급여부
            $insertData['point'] = $this->pointConfig->joinPoint;
        }

        $insertData['uid'] = $request->input('uid');
        $insertData['password'] = Hash::make($request->input('upass'));
        $insertData['name'] = $request->input('name');
        $insertData['pcs'] = $request->input('pcs');
        $insertData['nick'] = $request->input('nick');

        $insertData['mstatus'] = 'ing';
        if(empty($request->input('joinType'))) {
            $insertData['email'] = $request->input('uid');
        }
        
        $member = $this->memberRepository->insertMember($insertData);
        if(!empty($member)) {
             if($this->pointConfig && $this->pointConfig->joinPointUse == 'yes') {
                $pointParams['pointMsg'] = $this->pointRepository->PCODES['join'];
                $pointParams['user_id'] = $member->id;
                $pointParams['ptype'] = 'plus';
                $pointParams['point'] = $this->pointConfig->joinPoint;
                $pointParams['pcode'] = 'join';
                $this->pointRepository->insertPoint($pointParams);
            }

            $guard =  $this->authManager->guard('jwt');
            $credentials = [
                'uid'=>$request->input('uid'),
                'password'=>$request->input('upass'),
            ];
            $token = $guard->attempt($credentials);
            if($token) {
                $tokenExpire = time()+ ($guard->factory()->getTTL() * 60);
                $data['tokenInfo'] = ['token' => $token,
                               'token_type' => 'bearer',
                               'expires_in' =>  $tokenExpire];
                $data['userInfo'] = $guard->user();
                $status = 'success';

                if(!empty($insertData['email'])) {
                    $eventParams['to'] = $insertData['email'];
                    $eventParams['regDate'] = date('Y-m-d');
                    $eventParams['userName']  = $insertData['name'];
                    $eventParams['userId']  = $insertData['uid'];
                    $eventParams['type'] = 'join';
                    \Event::dispatch(new \App\Events\MailEvent($eventParams));
                }
                if(!empty($insertData['pcs'])) {
                    $smsParams['to'] = $request->input('pcs');
                    $smsParams['regDate'] = date('Y-m-d');
                    $smsParams['userName']  = $insertData['name'];
                    $smsParams['userId']  = $insertData['uid'];
                    $smsParams['type'] = 'join';
                    \Event::dispatch(new \App\Events\SmsEvent($smsParams));
                }
            } else {
                $data = 'failToken';
                $status = 'message';
            }
        } else {
            $status = 'error';
            $data = '';
        }
        return ['status'=>$status,'data'=>$data];

    }

    public function getMemberConfig() {
        $config = $this->config;
        if(!empty($config->snsInfo)) {
            if(!empty($config->snsInfo->ka)) {
                $config->snsInfo->ka->redirectUrl = str_replace('admin.','',$config->snsInfo->ka->redirectUrl);
            }
            if(!empty($config->snsInfo->nv)) {
                $config->snsInfo->nv->redirectUrl = str_replace('admin.','',$config->snsInfo->nv->redirectUrl);
            }
            if(!empty($config->snsInfo->fb)) {
                $config->snsInfo->fb->redirectUrl = str_replace('admin.','',$config->snsInfo->fb->redirectUrl);
            }

        }

        return $this->config;
    }
    public function getMemberAgree() {
        return $this->agree;
    }
    // 아이디 중복체크
    public function checkUid(string $uid) {
        $memberInfo = $this->memberRepository->checkUid($uid);
        if($memberInfo) {
            $data = 'disable';
        } else {
            $data = 'able';
        }
        return ['status'=>'success','data'=>$data];
    }
    // 닉네임 중복체크
    public function checkNick(string $nick) {
        $memberInfo = $this->memberRepository->checkNick($nick);
        if($memberInfo) {
            $data = 'disable';
        } else {
            $data = 'able';
        }
        return ['status'=>'success','data'=>$data];
    }
    // 인증번호 메일발송
    public function sendAuthEmail(Request $request) {

        $authNumber = mt_rand(10000,99999);
        $request->session()->put('authNumber',$authNumber);

        $eventParams['to'] = $request->input('email');
        $eventParams['authNumber'] = $authNumber;
        $eventParams['type'] = 'joinAuth';
        \Event::dispatch(new \App\Events\MailEvent($eventParams));
         return ['status'=>'success','data'=>''];
    }
    // 인증번호 휴대폰발송
    public function sendAuthPcs(Request $request) {

        $authNumber = mt_rand(10000,99999);
        $request->session()->put('authNumber',$authNumber);

        $smsParams['to'] = $request->input('pcs');
        $smsParams['authNumber'] = $authNumber;
        $smsParams['type'] = 'joinAuth';
        $sms = app()->make(SendSms::class);
        $result = $sms->send($smsParams);

        if($result=='success') {
            $status = 'success';
        } else {
            $status = 'message';
            if(!$result)$result = '잘못된 접근입니다';
        }
        return ['status'=>$status,'data'=>$result];
    }
   // 인증번호 확인
    public function confirmAuthNumber(Request $request) {
        $authNumber = $request->input('authNumber');

        if($request->session()->get('authNumber') == $authNumber) {
            return ['status'=>'success','data'=>''];
        } else {
            return ['status'=>'message','data'=>''];
        }
    }


    // 닉네임 변경시 중복체크 확인
    public function checkMyMemberNick(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $memberInfo = $this->memberRepository->checkMyNick($this->isLoginInfo->id,$request->input('nick'));
        if($memberInfo) {
            $data = 'disable';
        } else {
            $data = 'able';
        }
        return ['status'=>'success','data'=>$data];
    }

    // 회원 정보변경
    public function updateMemberInfo(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        if($this->config && $this->config->authType=='pcs') {

            if(!empty($request->input('pcs'))) {
                $authNumber = $request->input('authNumber');
                if($request->session()->get('authNumber') != $authNumber) {
                    return ['status'=>'message','data'=>'wrongAuthNumber'];
                }
                $updateData['pcs'] = $request->input('pcs');
            }
        } else if(!empty($request->input('pcs'))) {
            $updateData['pcs'] = (!empty($request->input('pcs')))?$request->input('pcs'):'';
        }
        if(!empty($request->input('upass')) && !empty($request->input('nowupass'))) {
            if(Hash::check($request->input('nowupass'),$this->isLoginInfo->password)) {
                $updateData['password'] = Hash::make($request->input('upass'));
            } else {
                    return ['status'=>'message','data'=>'wrongNowpass'];
            }
        }
        $updateData['name'] = $request->input('name');
        if(!empty($request->input('nick'))) {
            $updateData['nick'] = $request->input('nick');
        }
        $member = $this->memberRepository->updateMember($this->isLoginInfo->id,$updateData);
        if(!empty($member)) {
            $status = 'success';
            $data = '';

        } else {
            $status = 'error';
            $data = '';
        }
        return ['status'=>$status,'data'=>$data];

    }

    // 비밀번호 찾기
    public function findMemberUpass(Request $request) {
        if(!empty($this->isLoginInfo)) {
            return ['status'=>'error','data'=>''];
        }
        $memberInfo = $this->memberRepository->checkUid($request->input('email'));
        if($memberInfo && $memberInfo->id) {
            if($memberInfo->name != $request->input('name')) {
                return ['status'=>'message','data'=>''];
            }
            $newpass = base64_encode(mt_rand(10000,99999));
            $updateData['password'] = Hash::make($newpass);
            $result = $this->memberRepository->updateMember($memberInfo->id,$updateData);
            if($result) {
                $eventParams['to'] = $request->input('email');
                $eventParams['newpass'] = $newpass;
                $eventParams['userName']  = $memberInfo->name;
                $eventParams['userId']  = $memberInfo->uid;

                $eventParams['type'] = 'findUpass';
                \Event::dispatch(new \App\Events\MailEvent($eventParams));
                return ['status'=>'success','data'=>''];
            }
        } else {
            return ['status'=>'message','data'=>''];
        }

    }

    // 회원이미지 변경
    public function updateMemberImage(Request $params) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $params['type'] = 'image';
        $filePath = 'public/board';
        $imgUrl = '/boardImages/';
        $imgName = uploadFile($params,'image',$filePath,$params);
        $imgUrl = $imgUrl.$imgName;
        
        $member = $this->memberRepository->updateMember($this->isLoginInfo->id,['img'=>$imgUrl]);

        return ['status'=>'success','data'=>$member];
    }
}
