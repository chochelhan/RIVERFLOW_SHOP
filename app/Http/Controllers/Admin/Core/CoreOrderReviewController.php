<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeOrderReviewService;


class CoreOrderReviewController extends Controller
{
    protected $orderReviewService;
    public function __construct(CustomizeOrderReviewService $orderReviewService) {
        $this->orderReviewService = $orderReviewService;

    }

    public function blindReview(Request $request) {
        $data = $this->orderReviewService->blindReview($request);

        return restResponse($data);
    }

}
