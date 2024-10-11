<?php

namespace App\Http\Controllers\Api\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Api\Core\TrackerService;

class TrackerController extends Controller
{
    protected $trackerService;

    public function __construct(TrackerService $trackerService) {
        $this->trackerService = $trackerService;

    }

    public function recordTracker(Request $request) {
        $data = $this->trackerService->recordTracker($request);
        return restResponse($data);
        
    }


}
