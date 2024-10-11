<?php

namespace App\Listeners;

use App\Events\MailEvent;
//use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class MailSubscriber
{


    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MailEvent $event)
    {

        $params = $event->getParams();
        if(!empty($params['type'])) {
            $gid = $params['type'];
            $tblName = config('tables.smsEmailSetting');
            $baseInfo = DB::table($tblName)->where('gtype','email')->where('gid','base')->first();
            if($baseInfo && $baseInfo->content) {
                $info = json_decode($baseInfo->content);
                $sendName= $info->name;
                $sendEmail= $info->email;
                if($sendEmail && $sendName) {
                    $row = DB::table($tblName)->where('gtype','email')->where('gid',$gid)->first();
					if($row!=null) {
						$guse = ($gid == 'joinAuth') ? 'yes' : $row->guse;
						if ($row && $row->content && $guse == 'yes') {
							Mail::to($params['to'])->send(new SendMail($params));
						}
					}
                }
            }
        }

    }
}
