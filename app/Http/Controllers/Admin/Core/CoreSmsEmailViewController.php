<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeSmsEmailService;


class CoreSmsEmailViewController extends Controller
{
    protected $smsEmailService;
    public function __construct(CustomizeSmsEmailService $smsEmailService) {
        $this->smsEmailService = $smsEmailService;

    }

    /***  이메일 설정정보 불러오기 **/
    public function getSmsEmailSetting(Request $request) {
        $data = $this->smsEmailService->getSmsEmailSetting($request);

        return restResponse($data);
    }
}