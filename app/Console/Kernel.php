<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {

            $siteTable = config('tables.settingSite');
            $siteInfo = DB::table($siteTable)->select('company')->first();
            $ocday = config('order.ocday');
            if(!empty($siteInfo) && !empty($siteInfo->company)) {
                $companyInfo = json_decode($siteInfo->company);
                if(!empty($companyInfo->ocday)) {
                    $ocday = $companyInfo->ocday;
                }
            }

            $tableName = config('tables.orderProduct');
            $historyTable = config('tables.orderHistory');
            $dcDate = date('Y-m-d H:i:s',mktime(date('H'),date('i'),date('s'),date('m'),date('d') - $ocday,date('Y')));
            $list = DB::table($tableName)->where('ostatus','DC')->where('updated_at','<=',$dcDate)->get();
            foreach($list as $val) {
                DB::table($tableName)->where('id',$val->id)->update(['ostatus'=>'OC']);

                $inparams = [];
                $inparams['created_at'] = date('Y-m-d H:i:s');
                $inparams['nowstatus'] = 'OC';
                $inparams['oldstatus'] = 'DC';
                $inparams['oid'] = $val->oid;
                $inparams['opid'] = $val->id;
                DB::table($historyTable)->insert($inparams);
            }

        })->everyThreeHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
