<?php

namespace App\Listeners;

use App\Events\OrderHistoryEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderHistorySubscriber
{


    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderHistory  $event
     * @return void
     */
    public function handle(OrderHistoryEvent $event)
    {

        $params = $event->getParams();

        $inparams['created_at'] = date('Y-m-d H:i:s');
        $inparams['nowstatus'] = $params['nowstatus'];

        if(!empty($params['content']))$inparams['content'] = json_encode($params['content']);

        foreach($params['oldstatus'] as $opid=>$idData) {
            $inparams['oldstatus'] = $idData['ostatus'];
            $inparams['oid'] = $idData['oid'];
            $inparams['opid'] = $opid;

            $tblName = config('tables.orderHistory');
            DB::table($tblName)->insert($inparams);
        }

    }
}
