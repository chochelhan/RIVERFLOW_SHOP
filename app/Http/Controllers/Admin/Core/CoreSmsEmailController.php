<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeSmsEmailService;
use Illuminate\Http\response;

class CoreSmsEmailController extends Controller
{
    protected $smsEmailService;
    public function __construct(CustomizeSmsEmailService $smsEmailService) {
        $this->smsEmailService = $smsEmailService;

    }

    /***  이메일 설정정보 수정/저장 **/
    public function updateSmsEmailSetting(Request $request) {
        $data = $this->smsEmailService->updateSmsEmailSetting($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }
}
