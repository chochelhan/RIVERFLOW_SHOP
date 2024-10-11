<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiMemberService;

/**
* 회원
*
**/
class CoreApiMemberViewController extends CoreApiAuthHeaderController
{

    protected $memberService;

    public function __construct(Request $request,CustomizeApiMemberService $memberService) {
        parent::__construct($request);
        $this->memberService = $memberService;

    }

    public function getMemberConfig() {

        $data = $this->memberService->getMemberConfig();
        return apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }
    // 약관정보
    public function getMemberAgree() {

        $data = $this->memberService->getMemberAgree();
        return apiResponse(['status'=>'success','data'=>$data],$this->newToken);
    }
}
