<?php

namespace App\Listeners;

use App\Events\SmsEvent;
use App\Sms\SendSms;

class SmsSubscriber
{


    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(SmsEvent $event)
    {


        $params = $event->getParams();
        $sms = app()->make(SendSms::class);
        $sms->send($params);
    }

}
