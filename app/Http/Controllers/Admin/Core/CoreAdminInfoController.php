<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeMemberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
/**
* 괸리자 정보
*
**/
class CoreAdminInfoController extends Controller
{
    protected $memberService;

    public function __construct(CustomizeMemberService $memberService) {
        $this->memberService = $memberService;
    }

    /**
    * 관리자 정보
    **/
    public function getAdminInfo() {
        $data = Auth::user();
        $status = ($data)?'success':'fail';

        return restResponse(['status'=>$status,'data'=>$data]);
    }

    /**
    * 관리자 정보 수정
    **/
    public function updateAdminInfo(Request $request) {

        if(!$request->has(['name','nowUpass'])) {
            return restResponse(['status'=>'emptyField','data'=>'']);
        }

        $adminInfo = Auth::user();
        if(Hash::check($request->input('nowUpass'),$adminInfo->password)) {
            $updateData = $request->all();
            $id = $adminInfo->id;
            if(!empty($request->input('upass'))) {
                $updateData['password'] = Hash::make($request->input('upass'));
            }
            $data = $this->memberService->updateAdminInfo($id,$updateData);
            return restResponse($data);
        } else {
            return restResponse(['status'=>'message','data'=>'wrongNowpass']);
        }


    }

}
