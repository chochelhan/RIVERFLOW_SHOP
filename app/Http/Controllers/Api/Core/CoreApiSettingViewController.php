<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiSettingService;

/**
* 회원
*
**/
class CoreApiSettingViewController extends CoreApiAuthHeaderController
{

    protected $settingService;

    public function __construct(Request $request,CustomizeApiSettingService $settingService) {
        parent::__construct($request);
        $this->settingService = $settingService;

    }

    public function getBase() {

        $data = $this->settingService->getBase();
	    return apiResponse(['status'=>'success','data'=>$data],$this->newToken);

    }

    public function getMain() {

	    $data = $this->settingService->getMain();
        return apiResponse(['status'=>'success','data'=>$data],$this->newToken);
	
    }
}
