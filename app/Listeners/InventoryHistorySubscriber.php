<?php

namespace App\Listeners;

use App\Events\InventoryHistoryEvent;
use Illuminate\Support\Facades\DB;

class InventoryHistorySubscriber
{


    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(InventoryHistoryEvent $event)
    {

        $params = $event->getParams();

        $inparams['created_at'] = date('Y-m-d H:i:s');
        $inparams['ivt_id'] =  $params['ivt_id'];
        $inparams['type'] = $params['type'];
        $inparams['content'] = json_encode($params['content']);
        $tblName = config('tables.inventoryHistory');
        DB::table($tblName)->insert($inparams);

    }
}
