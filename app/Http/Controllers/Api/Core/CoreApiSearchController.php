<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Api\Core\CoreApiAuthHeaderController;
use Illuminate\Http\Request;

use App\Services\Api\Customize\CustomizeApiSearchService;

class CoreApiSearchController extends CoreApiAuthHeaderController
{
    protected $searchService;

    public function __construct(Request $request, CustomizeApiSearchService $searchService) {
         parent::__construct($request);
        $this->searchService = $searchService;

    }

    public function getCategoryList() {

        $data = $this->searchService->getCategoryList();
        return $this->apiResponse($data,$this->newToken);

    }


    public function searchData(Request $request) {
        if(!$request->has(['keyword'])) {
            $data = ['status'=>'emptyField','data'=>''];
            return $this->apiResponse($data,$this->newToken);
        }
        $data = $this->searchService->searchData($request->input('keyword'));
        return $this->apiResponse($data,$this->newToken);

    }

}
