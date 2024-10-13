<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $params;

    /**
     * Create a new message instance.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if(!empty($this->params['type'])) {
            $gid = $this->params['type'];
            $tblName = config('tables.smsEmailSetting');
            $baseInfo = DB::table($tblName)->where('gtype','email')->where('gid','base')->first();

            if($baseInfo && $baseInfo->content) {
                $info = json_decode($baseInfo->content);
                $sendName= $info->name;
                $sendEmail= $info->email;
                if($sendEmail && $sendName) {
                    $row = DB::table($tblName)->where('gtype','email')->where('gid',$gid)->first();
                    $guse = ($gid=='joinAuth')?'yes':$row->guse;


                    if($row && $row->content && $guse=='yes') {
                        $contentInfo = json_decode($row->content);
                        $subject = $contentInfo->subject;
                        $emailParams = [];
                        $template = '';
                        switch($gid) {
                            case 'joinAuth':
                                $emailParams = ['authNumber'=>$this->params['authNumber']];
                                $template = 'mail.joinAuth';
                            break;
                            case 'findUpass':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $userId = (empty($this->params['userId']))?'':$this->params['userId'];
                                $emailParams = ['newpass'=>$this->params['newpass'],'userName'=>$userName,'userId'=>$userId];
                                $template = 'mail.findUpass';
                            break;
                            case 'join':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $userId = (empty($this->params['userId']))?'':$this->params['userId'];
                                $regDate = (empty($this->params['regDate']))?'':$this->params['regDate'];
                                $emailParams = ['regDate'=>$regDate,'userName'=>$userName,'userId'=>$userId];
                                $template = 'mail.join';
                            break;
                            case 'notpay':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $userId = (empty($this->params['userId']))?'':$this->params['userId'];
                                $orderDate = (empty($this->params['orderDate']))?'':$this->params['orderDate'];
                                $orderInfo = (empty($this->params['orderInfo']))?'':$this->params['orderInfo'];
                                $paymentInfo = (empty($this->params['paymentInfo']))?'':$this->params['paymentInfo'];
                                $bankExpire  = (empty($this->params['bankExpire']))?'':$this->params['bankExpire'];
                                $accountInfo  = (empty($this->params['accountInfo']))?'':$this->params['accountInfo'];

                                $emailParams = ['orderDate'=>$orderDate,
                                                'userName'=>$userName,
                                                'userId'=>$userId,
                                                'orderInfo' =>$orderInfo,
                                                'paymentInfo' =>$paymentInfo,
                                                'bankExpire'  =>$bankExpire,
                                                'accountInfo' =>$accountInfo];
                                $template = 'mail.notpay';
                            break;
                            case 'income':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $orderDate = (empty($this->params['orderDate']))?'':$this->params['orderDate'];
                                $orderInfo = (empty($this->params['orderInfo']))?'':$this->params['orderInfo'];
                                $paymentInfo = (empty($this->params['paymentInfo']))?'':$this->params['paymentInfo'];

                                $emailParams = ['orderDate'=>$orderDate,
                                                'userName'=>$userName,
                                                'orderInfo' =>$orderInfo,
                                                'paymentInfo' =>$paymentInfo];
                                $template = 'mail.income';
                            break;
                            case 'delivery':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $orderDate = (empty($this->params['orderDate']))?'':$this->params['orderDate'];
                                $orderInfo = (empty($this->params['orderInfo']))?'':$this->params['orderInfo'];
                                $paymentInfo = (empty($this->params['paymentInfo']))?'':$this->params['paymentInfo'];

                                $emailParams = ['orderDate'=>$orderDate,
                                                'userName'=>$userName,
                                                'orderInfo' =>$orderInfo,
                                                'paymentInfo' =>$paymentInfo];
                                $template = 'mail.delivery';
                            break;
                            case 'CC':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $cancleProduct = (empty($this->params['cancleProduct']))?'':$this->params['cancleProduct'];
                                $refundPrice  = (empty($this->params['refundPrice']))?'':$this->params['refundPrice'];
                                $emailParams = ['userName'=>$userName,
                                                'cancleProduct' =>$cancleProduct,
                                                'refundPrice' =>$refundPrice];
                                $template = 'mail.CC';
                            break;
                            case 'RC':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $returnProduct = (empty($this->params['returnProduct']))?'':$this->params['returnProduct'];
                                $refundPrice  = (empty($this->params['refundPrice']))?'':$this->params['refundPrice'];
                                $emailParams = ['userName'=>$userName,
                                                'returnProduct' =>$returnProduct,
                                                'refundPrice' =>$refundPrice];
                                $template = 'mail.RC';
                            break;
                            case 'EC':
                                $userName = (empty($this->params['userName']))?'':$this->params['userName'];
                                $exchangeProduct = (empty($this->params['exchangeProduct']))?'':$this->params['exchangeProduct'];
                                $emailParams = ['userName'=>$userName,
                                                'exchangeProduct' =>$exchangeProduct];
                                $template = 'mail.EC';
                            break;
                        }
                        return $this->from($sendEmail,$sendName)->view($template)->subject($subject)->with($emailParams);
                    }
                }
            }
        }
    }
}
